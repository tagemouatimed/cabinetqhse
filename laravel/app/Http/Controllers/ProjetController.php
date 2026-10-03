<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Client;
use App\Models\PhaseAccompagnement;
use App\Models\Projet;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $tous = Projet::with('client')->get();

        $stats = [
            'total' => $tous->count(),
            'en_cours' => $tous->where('statut', 'en_cours')->count(),
            'en_retard' => $tous->filter(fn ($p) => $p->est_en_retard)->count(),
            'termine' => $tous->where('statut', 'termine')->count(),
        ];

        $projets = Projet::with('client')
            ->when($request->statut === 'en_cours', fn ($q) => $q->where('statut', 'en_cours'))
            ->when($request->statut === 'termine', fn ($q) => $q->where('statut', 'termine'))
            ->when($request->statut === 'planifie', fn ($q) => $q->where('statut', 'planifie'))
            ->orderByDesc('date_debut')
            ->get();

        if ($request->statut === 'en_retard') {
            $projets = $projets->filter(fn ($p) => $p->est_en_retard);
        }

        return view('projets.index', compact('projets', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();

        return view('projets.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'nom' => 'required|string|max:255',
            'norme' => 'nullable|string|max:255',
            'secteur' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date',
        ]);
        $data['statut'] = 'planifie';

        $projet = Projet::create($data);

        return redirect()->route('projets.show', $projet)->with('success', 'Projet créé.');
    }

    public function show(Projet $projet)
    {
        $projet->load('plansAction.actions', 'plansAction.phase.actionsPreetablies', 'notes', 'rapportsReunion', 'pieceJointes');
        $phases = PhaseAccompagnement::with('actionsPreetablies')->orderBy('ordre')->get();

        return view('projets.show', compact('projet', 'phases'));
    }

    public function edit(Projet $projet)
    {
        $clients = Client::orderBy('nom')->get();

        return view('projets.edit', compact('projet', 'clients'));
    }

    public function update(Request $request, Projet $projet)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'nom' => 'required|string|max:255',
            'norme' => 'nullable|string|max:255',
            'secteur' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date',
            'statut' => 'required|in:planifie,en_cours,en_retard,termine,suspendu',
        ]);

        $projet->update($data);

        return redirect()->route('projets.show', $projet)->with('success', 'Projet mis à jour.');
    }

    public function destroy(Projet $projet)
    {
        $projet->delete();

        return redirect()->route('projets.index')->with('success', 'Projet supprimé.');
    }

    public function export()
    {
        $rows = Projet::with('client')->get()->map(fn ($p) => [
            $p->nom, $p->client->nom_complet, $p->norme, $p->statut, $p->avancement . '%',
            $p->date_fin_prevue?->format('d/m/Y'),
        ]);

        return Excel::download(
            new TableauExport(['Projet', 'Client', 'Norme', 'Statut', 'Avancement', 'Fin prévue'], $rows),
            'projets.xlsx'
        );
    }
}
