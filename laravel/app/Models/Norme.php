<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Norme extends Model
{
    protected $fillable = ['code', 'libelle'];

    public function exigences()
    {
        return $this->hasMany(ExigenceNorme::class);
    }

    public function planModele()
    {
        return $this->hasOne(PlanAudit::class)->where('est_modele', true);
    }
}
