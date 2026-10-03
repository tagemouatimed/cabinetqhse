<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    protected $fillable = ['participant_formation_id', 'numero', 'date_delivrance'];

    protected $casts = ['date_delivrance' => 'date'];

    public function participant()
    {
        return $this->belongsTo(ParticipantFormation::class, 'participant_formation_id');
    }

    public static function prochainNumero(): string
    {
        $annee = now()->format('y');
        $dernier = static::where('numero', 'like', "CERT%/{$annee}")
            ->get()
            ->map(fn ($c) => (int) substr($c->numero, 4, strpos($c->numero, '/') - 4))
            ->max() ?? 0;

        return sprintf('CERT%02d/%s', $dernier + 1, $annee);
    }
}
