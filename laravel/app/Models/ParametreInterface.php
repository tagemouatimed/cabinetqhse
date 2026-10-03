<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametreInterface extends Model
{
    protected $table = 'parametres_interface';
    protected $fillable = ['module', 'couleur', 'ordre'];
}
