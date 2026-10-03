<?php

namespace App\Http\Controllers;

use App\Models\Norme;
use Illuminate\Http\Request;

class NormeController extends Controller
{
    public function index()
    {
        $normes = Norme::withCount('exigences')->orderBy('code')->get();

        return view('audits.normes', compact('normes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|max:255|unique:normes,code', 'libelle' => 'required|string|max:255']);
        Norme::create($data);

        return back()->with('success', 'Norme ajoutée.');
    }

    public function storeExigence(Request $request, Norme $norme)
    {
        $data = $request->validate(['reference' => 'required|string|max:50', 'libelle' => 'required|string']);
        $norme->exigences()->create($data);

        return back()->with('success', 'Exigence ajoutée.');
    }
}
