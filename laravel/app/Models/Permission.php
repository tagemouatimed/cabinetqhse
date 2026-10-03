<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['role', 'module', 'peut_voir', 'peut_ajouter', 'peut_modifier', 'peut_supprimer', 'peut_imprimer'];
    protected $casts = [
        'peut_voir' => 'boolean', 'peut_ajouter' => 'boolean', 'peut_modifier' => 'boolean',
        'peut_supprimer' => 'boolean', 'peut_imprimer' => 'boolean',
    ];

    public static function autorise(string $role, string $module, string $action): bool
    {
        $colonne = 'peut_' . $action; // action: voir|ajouter|modifier|supprimer|imprimer

        return (bool) static::where('role', $role)->where('module', $module)->value($colonne);
    }
}
