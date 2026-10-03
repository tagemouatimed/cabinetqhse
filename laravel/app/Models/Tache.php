<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $table = 'taches';
    protected $fillable = ['titre', 'description', 'date_echeance', 'responsable', 'statut', 'liee_type', 'liee_id'];
    protected $casts = ['date_echeance' => 'date'];

    public function liee()
    {
        return $this->morphTo();
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->statut !== 'faite' && $this->date_echeance && now()->greaterThan($this->date_echeance);
    }
}
