<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
    protected $table = 'devis_lignes';

    protected $fillable = [
        'devis_id', 'prestation_id', 'designation', 'quantite', 'prix_unitaire', 'montant',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }
}
