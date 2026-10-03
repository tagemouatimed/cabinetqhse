<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantFormation extends Model
{
    protected $table = 'participants_formation';
    protected $fillable = ['formation_id', 'nom', 'prenom', 'present', 'note_satisfaction'];
    protected $casts = ['present' => 'boolean'];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function certificat()
    {
        return $this->hasOne(Certificat::class);
    }
}
