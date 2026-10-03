<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAction extends Model
{
    protected $table = 'plans_action';

    protected $fillable = ['projet_id', 'titre', 'phase_id'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function phase()
    {
        return $this->belongsTo(PhaseAccompagnement::class, 'phase_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
