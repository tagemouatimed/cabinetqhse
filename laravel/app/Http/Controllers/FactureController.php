<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Prestation;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $toutes = Facture::where('est_avoir', false)->get();
        $stats = [
            'total' => $toutes->count(),
            'payees' => $toutes->where('statut', 'payee')->count(),
            'en_retard' => $toutes->filter(fn ($f) => $f->est_en_retard)->count(),
            'ca_total' => $toutes->sum('montant_ttc'),
        ];

        $factures = Facture::with('client', 'projet')
            ->when($request->statut && $request->statut !== 'en_retard', fn ($q) => $q->where('statut', $request->statut))
            ->orderByDesc('date_facture')
            ->get();

        if ($request->statut === 'en_retard') {
            $factures = $factures->filter(fn ($f) => $f->est_en_retard);
        }

        return view('facturation.factures.index', compact('factures', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $prestations = Prestation::orderBy('libelle')->get();
        $projets = Projet::with('client')->orderBy('nom')->get();
        $numero = Facture::prochainNumero();

        return view('facturation.factures.create', compact('clients', 'prestations', 'projets', 'numero'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'projet_id' => 'nullable|exists:projets,id',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
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

            $facture = Facture::create([
                'numero' => Facture::prochainNumero(),
                'client_id' => $data['client_id'],
                'projet_id' => $data['projet_id'] ?? null,
                'date_facture' => $data['date_facture'],
                'date_echeance' => $data['date_echeance'] ?? null,
                'statut' => 'brouillon',
                'montant_ht' => $montantHt,
                'frais_deplacement' => $frais ?: null,
                'taux_tva' => $data['taux_tva'],
                'montant_ttc' => round($montantHt * (1 + $data['taux_tva'] / 100), 2),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lignes'] as $ligne) {
                $facture->lignes()->create([
                    'prestation_id' => $ligne['prestation_id'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant' => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('factures.index')->with('success', 'Facture créée.');
    }

    public function show(Facture $facture)
    {
        $facture->load('lignes', 'client', 'projet', 'echeancesPaiement');

        return view('facturation.factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        $facture->load('lignes');
        $clients = Client::orderBy('nom')->get();
        $prestations = Prestation::orderBy('libelle')->get();
        $projets = Projet::with('client')->orderBy('nom')->get();

        return view('facturation.factures.edit', compact('facture', 'clients', 'prestations', 'projets'));
    }

    public function update(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'projet_id' => 'nullable|exists:projets,id',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
            'taux_tva' => 'required|numeric',
            'frais_deplacement' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string',
            'lignes.*.quantite' => 'required|numeric',
            'lignes.*.prix_unitaire' => 'required|numeric',
            'lignes.*.prestation_id' => 'nullable|exists:prestations,id',
        ]);

        DB::transaction(function () use ($data, $facture) {
            $montantLignes = collect($data['lignes'])->sum(fn ($l) => $l['quantite'] * $l['prix_unitaire']);
            $frais = (float) ($data['frais_deplacement'] ?? 0);
            $montantHt = $montantLignes + $frais;

            $facture->update([
                'client_id' => $data['client_id'],
                'projet_id' => $data['projet_id'] ?? null,
                'date_facture' => $data['date_facture'],
                'date_echeance' => $data['date_echeance'] ?? null,
                'montant_ht' => $montantHt,
                'frais_deplacement' => $frais ?: null,
                'taux_tva' => $data['taux_tva'],
                'montant_ttc' => round($montantHt * (1 + $data['taux_tva'] / 100), 2),
                'notes' => $data['notes'] ?? null,
            ]);

            $facture->lignes()->delete();

            foreach ($data['lignes'] as $ligne) {
                $facture->lignes()->create([
                    'prestation_id' => $ligne['prestation_id'] ?? null,
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant' => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('factures.show', $facture)->with('success', 'Facture mise à jour.');
    }

    public function destroy(Facture $facture)
    {
        $facture->delete();

        return redirect()->route('factures.index')->with('success', 'Facture supprimée.');
    }

    public function imprimer(Facture $facture)
    {
        $facture->load('lignes', 'client');

        return view('facturation.factures.imprimer', compact('facture'));
    }

    public function genererAvoir(Facture $facture)
    {
        $avoir = DB::transaction(function () use ($facture) {
            $avoir = Facture::create([
                'numero' => Facture::prochainNumero(),
                'client_id' => $facture->client_id,
                'date_facture' => now(),
                'statut' => 'brouillon',
                'montant_ht' => -$facture->montant_ht,
                'taux_tva' => $facture->taux_tva,
                'montant_ttc' => -$facture->montant_ttc,
                'est_avoir' => true,
                'facture_avoir_de_id' => $facture->id,
            ]);

            foreach ($facture->lignes as $ligne) {
                $avoir->lignes()->create([
                    'prestation_id' => $ligne->prestation_id,
                    'designation' => $ligne->designation,
                    'quantite' => $ligne->quantite,
                    'prix_unitaire' => -$ligne->prix_unitaire,
                    'montant' => -$ligne->montant,
                ]);
            }

            return $avoir;
        });

        return redirect()->route('factures.show', $avoir)->with('success', 'Avoir ' . $avoir->numero . ' généré.');
    }

    public function export()
    {
        $rows = Facture::with('client')->orderByDesc('date_facture')->get()->map(fn ($f) => [
            $f->numero, $f->client->nom_complet, $f->date_facture->format('d/m/Y'),
            $f->date_echeance?->format('d/m/Y'), $f->statut, $f->montant_ttc, $f->reste_a_payer,
        ]);

        return Excel::download(
            new TableauExport(['Numéro', 'Client', 'Date', 'Échéance', 'Statut', 'TTC', 'Reste à payer'], $rows),
            'factures.xlsx'
        );
    }
}
