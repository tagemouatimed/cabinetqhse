<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'prenom', 'raison_sociale', 'adresse', 'ice',
        'secteur_activite', 'telephone', 'email', 'notes',
    ];

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function echeancesPaiement()
    {
        return $this->hasMany(EcheancePaiement::class);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->raison_sociale ?: trim("{$this->prenom} {$this->nom}");
    }
}
