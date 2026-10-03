<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PieceJointe extends Model
{
    protected $table = 'pieces_jointes';
    protected $fillable = ['attachable_type', 'attachable_id', 'chemin_fichier', 'nom_original'];

    public function attachable()
    {
        return $this->morphTo();
    }
}
