<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionPreetablie extends Model
{
    protected $table = 'actions_preetablies';
    protected $fillable = ['phase_id', 'titre', 'description'];

    public function phase()
    {
        return $this->belongsTo(PhaseAccompagnement::class, 'phase_id');
    }
}
