<?php

namespace App\Http\Controllers;

use App\Exports\TableauExport;
use App\Models\Client;
use App\Models\Facture;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('nom')->get();

        $stats = [
            'total' => $clients->count(),
            'nouveaux_mois' => $clients->where('created_at', '>=', now()->startOfMonth())->count(),
            'avec_impaye' => Facture::where('est_avoir', false)
                ->whereColumn('montant_regle', '<', 'montant_ttc')
                ->distinct('client_id')
                ->count('client_id'),
            'secteurs' => $clients->pluck('secteur_activite')->filter()->unique()->count(),
        ];

        return view('facturation.clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('facturation.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:50',
            'secteur_activite' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client créé.');
    }

    public function edit(Client $client)
    {
        return view('facturation.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:50',
            'secteur_activite' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client mis à jour.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé.');
    }

    public function export()
    {
        $rows = Client::orderBy('nom')->get()->map(fn ($c) => [
            $c->nom_complet, $c->ice, $c->secteur_activite, $c->telephone, $c->email,
        ]);

        return Excel::download(
            new TableauExport(['Nom / Raison sociale', 'ICE', 'Secteur', 'Téléphone', 'Email'], $rows),
            'clients.xlsx'
        );
    }
}
