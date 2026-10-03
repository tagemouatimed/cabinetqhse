<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\Prestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DevisController extends Controller
{
    public function index(Request $request)
    {
        $tous = Devis::get();
        $stats = [
            'total' => $tous->count(),
            'brouillon' => $tous->where('statut', 'brouillon')->count(),
            'converti' => $tous->where('statut', 'converti')->count(),
            'montant_en_cours' => $tous->whereIn('statut', ['brouillon', 'envoye'])->sum('montant_ttc'),
        ];

        $devis = Devis::with('client')
            ->when($request->statut, fn ($q) => $q->where('statut', $request->statut))
            ->orderByDesc('date_devis')
            ->get();

        return view('facturation.devis.index', compact('devis', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $prestations = Prestation::orderBy('libelle')->get();
        $numero = Devis::prochainNumero();

        return view('facturation.devis.create', compact('clients', 'prestations', 'numero'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_devis' => 'required|date',
            'date_validite' => 'nullable|date',
            'taux_tva' => 'required|numeric',
            'frais_deplacement' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string',
            'lignes.*.quantite' => 'required|numeric',
            'lignes.*.prix_unitaire' => 'required|numeric',
            'lignes.*.prestation_id' => 'nullable|exists:prestations,id',
        ]);

        DB::transaction(function () use ($data) {
            $montantLignes = collect($data['lignes'])->sum(fn ($l) => $l['quantite'] * $l['prix_unitaire']);
            $frais = (float) ($data['frais_deplacement'] ?? 0);
            $montantHt = $montantLignes + $frais;

            $devis = Devis::create([
                'numero' => Devis::prochainNumero(),
                'client_id' => $data['client_id'],
                'date_devis' => $data['date_devis'],
                'date_validite' => $data['date_validite'] ?? null,
                'statut' => 'brouillon',
                'montant_ht' => $montantHt,
                'frais_deplacement' => $frais ?: null,
                'taux_tva' => $data['taux_tva'],
                'montant_ttc' => round($montantHt * (1 + $data['taux_tva'] / 100), 2),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $devis->lignes()->create([
                    'prestation_id' => $ligne['prestation_id'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant' => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('devis.index')->with('success', 'Devis créé.');
    }

    public function show(Devis $devis)
    {
        $devis->load('lignes', 'client');

        return view('facturation.devis.show', compact('devis'));
    }

    public function edit(Devis $devis)
    {
        $devis->load('lignes');
        $clients = Client::orderBy('nom')->get();
        $prestations = Prestation::orderBy('libelle')->get();

        return view('facturation.devis.edit', compact('devis', 'clients', 'prestations'));
    }

    public function update(Request $request, Devis $devis)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_devis' => 'required|date',
            'date_validite' => 'nullable|date',
            'taux_tva' => 'required|numeric',
            'frais_deplacement' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string',
            'lignes.*.quantite' => 'required|numeric',
            'lignes.*.prix_unitaire' => 'required|numeric',
            'lignes.*.prestation_id' => 'nullable|exists:prestations,id',
        ]);

        DB::transaction(function () use ($data, $devis) {
            $montantLignes = collect($data['lignes'])->sum(fn ($l) => $l['quantite'] * $l['prix_unitaire']);
            $frais = (float) ($data['frais_deplacement'] ?? 0);
            $montantHt = $montantLignes + $frais;

            $devis->update([
                'client_id' => $data['client_id'],
                'date_devis' => $data['date_devis'],
                'date_validite' => $data['date_validite'] ?? null,
                'montant_ht' => $montantHt,
                'frais_deplacement' => $frais ?: null,
                'taux_tva' => $data['taux_tva'],
                'montant_ttc' => round($montantHt * (1 + $data['taux_tva'] / 100), 2),
                'notes' => $data['notes'] ?? null,
            ]);

            $devis->lignes()->delete();

            foreach ($data['lignes'] as $ligne) {
                $devis->lignes()->create([
                    'prestation_id' => $ligne['prestation_id'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant' => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('devis.show', $devis)->with('success', 'Devis mis à jour.');
    }

    public function destroy(Devis $devis)
    {
        $devis->delete();

        return redirect()->route('devis.index')->with('success', 'Devis supprimé.');
    }

    public function imprimer(Devis $devis)
    {
        $devis->load('lignes', 'client');

        return view('facturation.devis.imprimer', compact('devis'));
    }

    public function convertirEnFacture(Devis $devis)
    {
        if ($devis->facture()->exists()) {
            return back()->with('error', 'Ce devis a déjà été converti en facture.');
        }

        $facture = DB::transaction(function () use ($devis) {
            $facture = Facture::create([
                'numero' => Facture::prochainNumero(),
                'client_id' => $devis->client_id,
                'devis_id' => $devis->id,
                'date_facture' => now(),
                'statut' => 'brouillon',
                'montant_ht' => $devis->montant_ht,
                'frais_deplacement' => $devis->frais_deplacement,
                'taux_tva' => $devis->taux_tva,
                'montant_ttc' => $devis->montant_ttc,
            ]);

            foreach ($devis->lignes as $ligne) {
                $facture->lignes()->create([
                    'prestation_id' => $ligne->prestation_id,
                    'designation' => $ligne->designation,
                    'quantite' => $ligne->quantite,
                    'prix_unitaire' => $ligne->prix_unitaire,
                    'montant' => $ligne->montant,
                ]);
            }

            $devis->update(['statut' => 'converti']);

            return $facture;
        });

        return redirect()->route('factures.show', $facture)->with('success', 'Devis converti en facture ' . $facture->numero . '.');
    }

    public function export()
    {
        $rows = Devis::with('client')->orderByDesc('date_devis')->get()->map(fn ($d) => [
            $d->numero, $d->client->nom_complet, $d->date_devis->format('d/m/Y'), $d->statut, $d->montant_ttc,
        ]);

        return Excel::download(
            new TableauExport(['Numéro', 'Client', 'Date', 'Statut', 'Montant TTC'], $rows),
            'devis.xlsx'
        );
    }
}
