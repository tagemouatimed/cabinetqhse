<?php

namespace App\Http\Controllers;

use App\Models\Prestation;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    public function index()
    {
        $prestations = Prestation::orderBy('libelle')->get();

        return view('parametrage.prestations', compact('prestations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['libelle' => 'required|string|max:255', 'prix_standard' => 'required|numeric']);
        Prestation::create($data);

        return back()->with('success', 'Prestation ajoutée.');
    }

    public function update(Request $request, Prestation $prestation)
    {
        $data = $request->validate(['libelle' => 'required|string|max:255', 'prix_standard' => 'required|numeric']);
        $prestation->update($data);

        return back()->with('success', 'Prestation mise à jour.');
    }

    public function destroy(Prestation $prestation)
    {
        $prestation->delete();

        return back()->with('success', 'Prestation supprimée.');
    }
}
