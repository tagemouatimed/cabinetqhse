<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\ConstatAudit;
use App\Models\RapportAudit;
use Illuminate\Http\Request;

class RapportAuditController extends Controller
{
    public function store(Audit $audit)
    {
        $rapport = $audit->rapport()->firstOrCreate([], ['statut' => 'brouillon']);

        return redirect()->route('audits.show', $audit)->with('success', 'Rapport (brouillon) créé.');
    }

    public function storeConstat(Request $request, RapportAudit $rapport)
    {
        $data = $request->validate([
            'type' => 'required|in:point_sensible,ecart_mineur,ecart_majeur,recommandation',
            'reference_norme' => 'nullable|string|max:50',
            'description' => 'required|string',
        ]);

        $constat = $rapport->constats()->create($data);

        // Écart mineur/majeur sur un audit rattaché à un projet -> action corrective générée automatiquement
        // dans le plan d'action du projet (suivi comme n'importe quelle autre action, module Tâches inclus).
        if (in_array($data['type'], ['ecart_mineur', 'ecart_majeur']) && $rapport->audit->projet_id) {
            $projet = $rapport->audit->projet;

            $plan = $projet->plansAction()->firstOrCreate(
                ['titre' => 'Actions correctives audit'],
            );

            $action = $plan->actions()->create([
                'titre' => 'Corriger : ' . \Illuminate\Support\Str::limit($data['description'], 100),
                'description' => $data['description'],
                'statut' => 'a_faire',
            ]);

            $constat->update(['action_corrective_id' => $action->id]);
        }

        return back()->with('success', 'Constat ajouté.' . (isset($action) ? ' Action corrective créée dans le projet.' : ''));
    }

    /**
     * Passage brouillon -> final : calcule et fige le score.
     */
    public function finaliser(RapportAudit $rapport)
    {
        $rapport->load('constats');
        $rapport->update(['statut' => 'final', 'score' => $rapport->calculerScore()]);

        return back()->with('success', 'Rapport finalisé — score : ' . $rapport->score . '/100.');
    }
}
