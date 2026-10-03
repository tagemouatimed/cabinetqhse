<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['plan_action_id', 'titre', 'description', 'date_echeance', 'responsable', 'statut'];

    protected $casts = ['date_echeance' => 'date'];

    public function planAction()
    {
        return $this->belongsTo(PlanAction::class);
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->statut !== 'realisee' && $this->date_echeance && now()->greaterThan($this->date_echeance);
    }
}
