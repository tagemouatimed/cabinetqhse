<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Audit;
use App\Models\Client;
use App\Models\Norme;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $tous = Audit::get();
        $stats = [
            'total' => $tous->count(),
            'planifie' => $tous->where('statut', 'planifie')->count(),
            'realise' => $tous->where('statut', 'realise')->count(),
            'en_retard' => $tous->filter(fn ($a) => $a->statut === 'planifie' && $a->date_planifiee->isPast())->count(),
        ];

        $audits = Audit::with('client', 'norme')
            ->when($request->statut === 'planifie', fn ($q) => $q->where('statut', 'planifie'))
            ->when($request->statut === 'realise', fn ($q) => $q->where('statut', 'realise'))
            ->orderBy('date_planifiee')
            ->get();

        if ($request->statut === 'en_retard') {
            $audits = $audits->filter(fn ($a) => $a->statut === 'planifie' && $a->date_planifiee->isPast());
        }

        return view('audits.index', compact('audits', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $normes = Norme::orderBy('code')->get();

        return view('audits.create', compact('clients', 'normes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'norme_id' => 'required|exists:normes,id',
            'type' => 'required|in:recurrent,ponctuel',
            'date_planifiee' => 'required|date',
            'recurrence_mois' => 'nullable|integer|min:1',
        ]);
        $data['statut'] = 'planifie';

        $audit = Audit::create($data);

        if ($modele = Norme::find($data['norme_id'])->planModele) {
            $modele->dupliquerPour($audit);
        }

        return redirect()->route('audits.show', $audit)->with('success', 'Audit planifié.');
    }

    public function show(Audit $audit)
    {
        $audit->load('client', 'norme', 'planAudit.lignes', 'rapport.constats');

        return view('audits.show', compact('audit'));
    }

    public function marquerRealise(Audit $audit)
    {
        $audit->update(['statut' => 'realise']);
        $audit->genererProchaineOccurrence();

        return back()->with('success', 'Audit marqué réalisé' . ($audit->type === 'recurrent' ? ' — prochaine occurrence générée.' : '.'));
    }

    public function export()
    {
        $rows = Audit::with('client', 'norme')->get()->map(fn ($a) => [
            $a->client->nom_complet, $a->norme->code, $a->type, $a->date_planifiee->format('d/m/Y'), $a->statut,
        ]);

        return Excel::download(new TableauExport(['Client', 'Norme', 'Type', 'Date planifiée', 'Statut'], $rows), 'audits.xlsx');
    }
}
