<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\PhaseAccompagnement;
use App\Models\PlanAction;
use App\Models\Projet;
use Illuminate\Http\Request;

class PlanActionController extends Controller
{
    public function store(Request $request, Projet $projet)
    {
        $data = $request->validate([
            'phase_id' => 'nullable|exists:phases_accompagnement,id',
            'titre' => 'required_without:phase_id|nullable|string|max:255',
        ]);

        // Le titre suit la phase pré-établie choisie si aucun titre libre n'est saisi.
        if (empty($data['titre']) && ! empty($data['phase_id'])) {
            $data['titre'] = PhaseAccompagnement::find($data['phase_id'])->nom;
        }

        $projet->plansAction()->create($data);

        return back()->with('success', "Plan d'action ajouté.");
    }

    public function storeAction(Request $request, PlanAction $planAction)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_echeance' => 'nullable|date',
            'responsable' => 'nullable|string|max:255',
        ]);
        $data['statut'] = 'a_faire';

        $planAction->actions()->create($data);

        return back()->with('success', 'Action ajoutée.');
    }

    public function updateStatutAction(Request $request, Action $action)
    {
        $data = $request->validate(['statut' => 'required|in:a_faire,en_cours,realisee,en_retard']);
        $action->update($data);
        $action->planAction->projet->recalculerAvancement();

        return back()->with('success', 'Statut mis à jour.');
    }
}
