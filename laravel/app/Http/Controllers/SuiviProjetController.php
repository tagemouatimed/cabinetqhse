<?php

namespace App\Http\Controllers;

use App\Models\PieceJointe;
use App\Models\Projet;
use App\Models\RapportReunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuiviProjetController extends Controller
{
    public function storeNote(Request $request, Projet $projet)
    {
        $data = $request->validate(['contenu' => 'required|string']);
        $data['auteur'] = $request->user()?->name;

        $projet->notes()->create($data);

        return back()->with('success', 'Note ajoutée.');
    }

    public function storeRapportReunion(Request $request, Projet $projet)
    {
        $data = $request->validate([
            'date_reunion' => 'required|date',
            'objet' => 'nullable|string|max:255',
            'compte_rendu' => 'required|string',
            'pieces_jointes.*' => 'nullable|file|max:20480',
        ]);

        $rapport = $projet->rapportsReunion()->create($data);

        foreach ($request->file('pieces_jointes', []) as $fichier) {
            // Stockage sur disque privé ('local'), jamais 'public' (données de laboratoire / audit confidentielles)
            $chemin = $fichier->store('rapports-reunion', 'local');

            $rapport->pieceJointes()->create([
                'chemin_fichier' => $chemin,
                'nom_original' => $fichier->getClientOriginalName(),
            ]);
        }

        return back()->with('success', 'Rapport de réunion ajouté.');
    }

    public function destroyPieceJointe(PieceJointe $pieceJointe)
    {
        Storage::disk('local')->delete($pieceJointe->chemin_fichier);
        $pieceJointe->delete();

        return back()->with('success', 'Pièce jointe supprimée.');
    }
}
