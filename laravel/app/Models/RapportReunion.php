<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportReunion extends Model
{
    protected $table = 'rapports_reunion';

    protected $fillable = ['projet_id', 'date_reunion', 'objet', 'compte_rendu'];
    protected $casts = ['date_reunion' => 'date'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function pieceJointes()
    {
        return $this->morphMany(PieceJointe::class, 'attachable');
    }
}
