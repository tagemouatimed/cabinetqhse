<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $base = Document::whereNull('document_precedent_id');

        $stats = [
            'total' => (clone $base)->count(),
            'normes' => (clone $base)->where('categorie', 'norme')->count(),
            'supports' => (clone $base)->where('categorie', 'support_formation')->count(),
            'autres' => (clone $base)->where('categorie', 'autre')->count(),
        ];

        $documents = Document::with('client')
            ->when($request->categorie, fn ($q) => $q->where('categorie', $request->categorie))
            ->whereNull('document_precedent_id')
            ->latest()
            ->get();

        return view('documents.index', compact('documents', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categorie' => 'required|in:norme,support_formation,autre',
            'titre' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'fichier' => 'required|file|max:51200',
        ]);

        $data['chemin_fichier'] = $request->file('fichier')->store('documents', 'local');
        unset($data['fichier']);

        Document::create($data);

        return back()->with('success', 'Document ajouté.');
    }

    public function nouvelleVersion(Request $request, Document $document)
    {
        $request->validate(['fichier' => 'required|file|max:51200']);
        $chemin = $request->file('fichier')->store('documents', 'local');

        $document->nouvelleVersion($chemin);

        return back()->with('success', 'Nouvelle version enregistrée.');
    }

    public function telecharger(Document $document)
    {
        return Storage::disk('local')->download($document->chemin_fichier, $document->titre);
    }
}
