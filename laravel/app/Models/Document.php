<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['categorie', 'titre', 'chemin_fichier', 'version', 'client_id', 'document_precedent_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function documentPrecedent()
    {
        return $this->belongsTo(Document::class, 'document_precedent_id');
    }

    /**
     * Nouvelle version d'un document existant (historisation, principe GED ISO 13485-like).
     */
    public function nouvelleVersion(string $cheminFichier): self
    {
        return static::create([
            'categorie' => $this->categorie,
            'titre' => $this->titre,
            'chemin_fichier' => $cheminFichier,
            'version' => $this->version + 1,
            'client_id' => $this->client_id,
            'document_precedent_id' => $this->id,
        ]);
    }
}
