<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseAccompagnement extends Model
{
    protected $table = 'phases_accompagnement';
    protected $fillable = ['nom', 'ordre'];

    public function actionsPreetablies()
    {
        return $this->hasMany(ActionPreetablie::class, 'phase_id')->orderBy('id');
    }
}
