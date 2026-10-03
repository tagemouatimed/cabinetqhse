<?php

namespace App\Http\Controllers;

use App\Models\Indicateur;
use App\Models\ValeurIndicateur;
use App\Services\IndicateurService;
use Illuminate\Http\Request;

class IndicateurController extends Controller
{
    public function index()
    {
        $indicateurs = Indicateur::with('valeurs')->get();
        $clesBase = IndicateurService::cles();

        return view('indicateurs.index', compact('indicateurs', 'clesBase'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:pourcentage,nombre,autre',
            'source' => 'required|in:manuel,base',
            'cle_base' => 'nullable|string|in:' . implode(',', array_keys(IndicateurService::cles())),
            'type_affichage' => 'required|in:courbe,barres,jauge,carte',
        ]);

        Indicateur::create($data);

        return back()->with('success', 'Indicateur créé.');
    }

    /**
     * Saisie manuelle d'une valeur pour une période (D1/D2 -> D1/D2*100 pour un %, ou valeur directe pour un nombre).
     */
    public function storeValeur(Request $request, Indicateur $indicateur)
    {
        $data = $request->validate([
            'periode' => 'required|string|max:20',
            'valeur_d1' => 'nullable|numeric',
            'valeur_d2' => 'nullable|numeric',
            'valeur' => 'nullable|numeric',
        ]);

        if ($indicateur->type === 'pourcentage' && $data['valeur_d1'] !== null && $data['valeur_d2']) {
            $data['valeur'] = round($data['valeur_d1'] / $data['valeur_d2'] * 100, 2);
        }

        ValeurIndicateur::updateOrCreate(
            ['indicateur_id' => $indicateur->id, 'periode' => $data['periode']],
            $data
        );

        return back()->with('success', 'Valeur enregistrée.');
    }

    /**
     * Calcule et enregistre la valeur du mois/année en cours pour un indicateur "source=base".
     */
    public function actualiserDepuisBase(Indicateur $indicateur)
    {
        if ($indicateur->source !== 'base' || ! $indicateur->cle_base) {
            return back()->with('error', "Cet indicateur n'est pas relié à la base.");
        }

        $periode = now()->format('Y-m');
        $valeur = IndicateurService::calculer($indicateur->cle_base);

        ValeurIndicateur::updateOrCreate(
            ['indicateur_id' => $indicateur->id, 'periode' => $periode],
            ['valeur' => $valeur]
        );

        return back()->with('success', 'Indicateur actualisé depuis la base.');
    }
}
