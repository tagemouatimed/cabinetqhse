<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\EcheancePaiement;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecouvrementController extends Controller
{
    public function index(Request $request)
    {
        $echeances = EcheancePaiement::with('client', 'facture')
            ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
            ->when($request->statut === 'en_retard', fn ($q) => $q->where('reglement_recu', false)->where('date_echeance', '<', now()))
            ->when($request->statut === 'regle', fn ($q) => $q->where('reglement_recu', true))
            ->when($request->statut === 'en_attente', fn ($q) => $q->where('reglement_recu', false)->where('date_echeance', '>=', now()))
            ->orderBy('date_echeance')
            ->get();

        // Lien Facturation <-> Recouvrement : chiffre d'affaires facturé, encaissé, reste à venir
        $caTotal = Facture::where('est_avoir', false)->sum('montant_ttc');
        $totalEncaisse = Facture::where('est_avoir', false)->sum('montant_regle');
        $resteAVenir = round($caTotal - $totalEncaisse, 2);
        $nbEcheancesEnRetard = EcheancePaiement::where('reglement_recu', false)->where('date_echeance', '<', now())->count();

        return view('facturation.recouvrement.index', compact(
            'echeances', 'caTotal', 'totalEncaisse', 'resteAVenir', 'nbEcheancesEnRetard'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'projet_id' => 'nullable|integer',
            'facture_id' => 'nullable|exists:factures,id',
            'partie' => 'required|string|max:255',
            'date_echeance' => 'required|date',
            'montant_prevu' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        EcheancePaiement::create($data);

        return back()->with('success', 'Échéance de paiement ajoutée.');
    }

    public function enregistrerReglement(Request $request, EcheancePaiement $echeance)
    {
        $data = $request->validate([
            'date_reglement' => 'required|date',
            'moyen_paiement' => 'required|in:virement,cheque,espece,traite,autre',
            'montant_regle' => 'required|numeric',
        ]);

        $data['reglement_recu'] = $data['montant_regle'] >= $echeance->montant_prevu;

        DB::transaction(function () use ($echeance, $data) {
            $echeance->update($data);

            if ($echeance->facture) {
                $echeance->facture->increment('montant_regle', $data['montant_regle']);

                if ($echeance->facture->fresh()->reste_a_payer <= 0) {
                    $echeance->facture->update(['statut' => 'payee']);
                } else {
                    $echeance->facture->update(['statut' => 'partiellement_payee']);
                }
            }
        });

        return back()->with('success', 'Règlement enregistré.');
    }

    public function destroy(EcheancePaiement $echeance)
    {
        $echeance->delete();

        return back()->with('success', 'Échéance supprimée.');
    }

    public function export()
    {
        $rows = EcheancePaiement::with('client')->orderBy('date_echeance')->get()->map(fn ($e) => [
            $e->client->nom_complet, $e->projet_id, $e->partie, $e->date_echeance->format('d/m/Y'),
            $e->montant_prevu, $e->montant_regle, $e->date_reglement?->format('d/m/Y'),
            $e->moyen_paiement, $e->reste, $e->reglement_recu ? 'Réglé' : 'En attente',
        ]);

        return Excel::download(
            new TableauExport(
                ['Client', 'Projet', 'Partie', 'Échéance', 'Montant prévu', 'Réglé', 'Date règlement', 'Moyen', 'Reste', 'Statut'],
                $rows
            ),
            'recouvrement.xlsx'
        );
    }
}
