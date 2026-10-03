<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicateur extends Model
{
    protected $fillable = ['nom', 'type', 'source', 'cle_base', 'type_affichage'];

    public function valeurs()
    {
        return $this->hasMany(ValeurIndicateur::class)->orderBy('periode');
    }
}
