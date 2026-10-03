<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $table = 'devis';

    protected $fillable = [
        'numero', 'client_id', 'date_devis', 'date_validite', 'statut',
        'montant_ht', 'frais_deplacement', 'taux_tva', 'montant_ttc', 'notes',
    ];

    protected $casts = [
        'date_devis' => 'date',
        'date_validite' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes()
    {
        return $this->hasMany(DevisLigne::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    public function getMontantTvaAttribute(): float
    {
        return round((float) $this->montant_ttc - (float) $this->montant_ht, 2);
    }

    public static function prochainNumero(): string
    {
        $annee = now()->format('y');
        $dernier = static::where('numero', 'like', "DV%/{$annee}")
            ->get()
            ->map(fn ($d) => (int) substr($d->numero, 2, strpos($d->numero, '/') - 2))
            ->max() ?? 0;

        $suivant = $dernier + 1;

        return sprintf('DV%02d/%s', $suivant, $annee);
    }
}
