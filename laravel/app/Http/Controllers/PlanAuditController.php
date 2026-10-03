<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Norme;
use App\Models\PlanAudit;
use Illuminate\Http\Request;

class PlanAuditController extends Controller
{
    /**
     * Créer/éditer le modèle standard de plan d'audit pour une norme.
     */
    public function storeModele(Request $request, Norme $norme)
    {
        $data = $request->validate(['nom' => 'required|string|max:255']);
        $norme->planModele()->create(array_merge($data, ['est_modele' => true]));

        return back()->with('success', 'Modèle de plan d\'audit créé.');
    }

    public function storeLigne(Request $request, PlanAudit $planAudit)
    {
        $data = $request->validate([
            'processus_audite' => 'required|string|max:255',
            'duree_minutes' => 'nullable|integer',
            'auditeur' => 'nullable|string|max:255',
            'audite' => 'nullable|string|max:255',
            'date_prevue' => 'nullable|date',
        ]);

        $planAudit->lignes()->create($data);

        return back()->with('success', 'Ligne ajoutée au plan d\'audit.');
    }
}
