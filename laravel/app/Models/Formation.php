<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = ['theme', 'client_id', 'formateur', 'date_planifiee', 'duree_heures', 'statut'];

    protected $casts = ['date_planifiee' => 'date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function participants()
    {
        return $this->hasMany(ParticipantFormation::class);
    }
}
