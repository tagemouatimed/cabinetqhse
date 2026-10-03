<?php

namespace App\Http\Controllers;

use App\Models\ParametreInterface;
use App\Models\Permission;
use App\Models\PhaseAccompagnement;
use Illuminate\Http\Request;

class ParametrageController extends Controller
{
    public function index()
    {
        $phases = PhaseAccompagnement::with('actionsPreetablies')->orderBy('ordre')->get();
        $permissions = Permission::orderBy('role')->orderBy('module')->get();
        $interface = ParametreInterface::orderBy('ordre')->get();
        $modules = ['facturation', 'projets', 'audits', 'formation', 'documents', 'indicateurs', 'taches', 'parametrage'];

        return view('parametrage.index', compact('phases', 'permissions', 'interface', 'modules'));
    }

    public function storePhase(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'ordre' => 'required|integer|min:0',
        ]);
        PhaseAccompagnement::create($data);

        return back()->with('success', 'Phase ajoutée.');
    }

    public function storeActionPreetablie(Request $request, PhaseAccompagnement $phase)
    {
        $data = $request->validate(['titre' => 'required|string|max:255', 'description' => 'nullable|string']);
        $phase->actionsPreetablies()->create($data);

        return back()->with('success', 'Action pré-établie ajoutée.');
    }

    public function updatePermission(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'peut_voir' => 'boolean', 'peut_ajouter' => 'boolean', 'peut_modifier' => 'boolean',
            'peut_supprimer' => 'boolean', 'peut_imprimer' => 'boolean',
        ]);

        Permission::updateOrCreate(['role' => $data['role'], 'module' => $data['module']], $data);

        return back()->with('success', 'Permissions mises à jour.');
    }

    public function updateInterface(Request $request)
    {
        $data = $request->validate([
            'module' => 'required|string|max:255',
            'couleur' => 'nullable|string|max:20',
            'ordre' => 'required|integer|min:0',
        ]);

        ParametreInterface::updateOrCreate(['module' => $data['module']], $data);

        return back()->with('success', 'Interface mise à jour.');
    }
}
