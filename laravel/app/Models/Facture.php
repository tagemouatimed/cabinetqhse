<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'numero', 'client_id', 'devis_id', 'projet_id', 'date_facture', 'date_echeance',
        'statut', 'montant_ht', 'frais_deplacement', 'taux_tva', 'montant_ttc', 'montant_regle',
        'est_avoir', 'facture_avoir_de_id', 'notes',
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'est_avoir' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function lignes()
    {
        return $this->hasMany(FactureLigne::class);
    }

    public function echeancesPaiement()
    {
        return $this->hasMany(EcheancePaiement::class);
    }

    public function getResteAPayerAttribute(): float
    {
        return round((float) $this->montant_ttc - (float) $this->montant_regle, 2);
    }

    public function getMontantTvaAttribute(): float
    {
        return round((float) $this->montant_ttc - (float) $this->montant_ht, 2);
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_echeance
            && now()->greaterThan($this->date_echeance)
            && $this->reste_a_payer > 0;
    }

    public static function prochainNumero(): string
    {
        $annee = now()->format('y');
        $dernier = static::where('numero', 'like', "CO%/{$annee}")
            ->get()
            ->map(fn ($f) => (int) substr($f->numero, 2, strpos($f->numero, '/') - 2))
            ->max() ?? 0;

        $suivant = $dernier + 1;

        return sprintf('CO%02d/%s', $suivant, $annee);
    }
}
