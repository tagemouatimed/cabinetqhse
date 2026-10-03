<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureLigne extends Model
{
    protected $table = 'facture_lignes';

    protected $fillable = [
        'facture_id', 'prestation_id', 'designation', 'quantite', 'prix_unitaire', 'montant',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }
}
