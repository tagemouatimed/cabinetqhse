<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Action;
use App\Models\Audit;
use App\Models\Formation;
use App\Models\Tache;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TacheController extends Controller
{
    public function index(Request $request)
    {
        $toutes = Tache::get();
        $stats = [
            'total' => $toutes->count(),
            'a_faire' => $toutes->where('statut', 'a_faire')->count(),
            'en_cours' => $toutes->where('statut', 'en_cours')->count(),
            'en_retard' => $toutes->filter(fn ($t) => $t->est_en_retard)->count(),
        ];

        $taches = Tache::query()
            ->when($request->statut === 'a_faire', fn ($q) => $q->where('statut', 'a_faire'))
            ->when($request->statut === 'en_cours', fn ($q) => $q->where('statut', 'en_cours'))
            ->when($request->statut === 'faite', fn ($q) => $q->where('statut', 'faite'))
            ->orderBy('date_echeance')
            ->get();

        if ($request->statut === 'en_retard') {
            $taches = $taches->filter(fn ($t) => $t->est_en_retard);
        }

        $actions = Action::with('planAction.projet')
            ->where('statut', '!=', 'realisee')
            ->orderBy('date_echeance')
            ->get();

        $audits = Audit::with('client', 'norme')
            ->where('statut', 'planifie')
            ->orderBy('date_planifiee')
            ->get();

        $formations = Formation::with('client')
            ->where('statut', 'planifiee')
            ->orderBy('date_planifiee')
            ->get();

        return view('taches.index', compact('taches', 'actions', 'audits', 'formations', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_echeance' => 'nullable|date',
            'responsable' => 'nullable|string|max:255',
        ]);
        $data['statut'] = 'a_faire';

        Tache::create($data);

        return back()->with('success', 'Tâche ajoutée.');
    }

    public function updateStatut(Request $request, Tache $tache)
    {
        $data = $request->validate(['statut' => 'required|in:a_faire,en_cours,faite']);
        $tache->update($data);

        return back()->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Tache $tache)
    {
        $tache->delete();

        return back()->with('success', 'Tâche supprimée.');
    }

    public function export()
    {
        $rows = Tache::get()->map(fn ($t) => [
            $t->titre, $t->responsable, $t->date_echeance?->format('d/m/Y'), $t->statut,
        ]);

        return Excel::download(new TableauExport(['Titre', 'Responsable', 'Échéance', 'Statut'], $rows), 'taches.xlsx');
    }
}
