<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExigenceNorme extends Model
{
    protected $table = 'exigences_norme';
    protected $fillable = ['norme_id', 'reference', 'libelle'];

    public function norme()
    {
        return $this->belongsTo(Norme::class);
    }
}
