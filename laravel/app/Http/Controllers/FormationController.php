<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Certificat;
use App\Models\Client;
use App\Models\Formation;
use App\Models\ParticipantFormation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $toutes = Formation::get();
        $stats = [
            'total' => $toutes->count(),
            'planifiee' => $toutes->where('statut', 'planifiee')->count(),
            'realisee' => $toutes->where('statut', 'realisee')->count(),
        ];

        $formations = Formation::withCount('participants')
            ->when($request->statut === 'planifiee', fn ($q) => $q->where('statut', 'planifiee'))
            ->when($request->statut === 'realisee', fn ($q) => $q->where('statut', 'realisee'))
            ->orderByDesc('date_planifiee')
            ->get();

        return view('formation.index', compact('formations', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();

        return view('formation.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'formateur' => 'nullable|string|max:255',
            'date_planifiee' => 'required|date',
            'duree_heures' => 'nullable|integer',
        ]);
        $data['statut'] = 'planifiee';

        $formation = Formation::create($data);

        return redirect()->route('formations.show', $formation)->with('success', 'Formation planifiée.');
    }

    public function show(Formation $formation)
    {
        $formation->load('participants.certificat', 'client');

        return view('formation.show', compact('formation'));
    }

    public function storeParticipant(Request $request, Formation $formation)
    {
        $data = $request->validate(['nom' => 'required|string|max:255', 'prenom' => 'required|string|max:255']);
        $formation->participants()->create($data);

        return back()->with('success', 'Participant ajouté.');
    }

    public function marquerRealisee(Request $request, Formation $formation)
    {
        $formation->update(['statut' => 'realisee']);

        foreach ($request->input('participants', []) as $id => $infos) {
            ParticipantFormation::find($id)?->update([
                'present' => isset($infos['present']),
                'note_satisfaction' => $infos['note_satisfaction'] ?? null,
            ]);
        }

        return back()->with('success', 'Formation clôturée.');
    }

    public function genererCertificat(ParticipantFormation $participant)
    {
        if ($participant->certificat) {
            return back()->with('error', 'Certificat déjà généré pour ce participant.');
        }

        $participant->certificat()->create([
            'numero' => Certificat::prochainNumero(),
            'date_delivrance' => now(),
        ]);

        return back()->with('success', 'Certificat généré.');
    }

    public function export()
    {
        $rows = Formation::with('client')->get()->map(fn ($f) => [
            $f->theme, $f->client?->nom_complet ?? 'Interne', $f->formateur, $f->date_planifiee->format('d/m/Y'), $f->statut,
        ]);

        return Excel::download(new TableauExport(['Thème', 'Client', 'Formateur', 'Date', 'Statut'], $rows), 'formations.xlsx');
    }
}
