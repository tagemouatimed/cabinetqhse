<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteProjet extends Model
{
    protected $table = 'notes_projet';
    protected $fillable = ['projet_id', 'contenu', 'auteur'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}
