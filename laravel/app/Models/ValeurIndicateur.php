<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValeurIndicateur extends Model
{
    protected $table = 'valeurs_indicateur';
    protected $fillable = ['indicateur_id', 'periode', 'valeur_d1', 'valeur_d2', 'valeur'];

    public function indicateur()
    {
        return $this->belongsTo(Indicateur::class);
    }
}
