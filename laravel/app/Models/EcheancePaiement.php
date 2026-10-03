<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcheancePaiement extends Model
{
    protected $table = 'echeances_paiement';

    protected $fillable = [
        'client_id', 'projet_id', 'facture_id', 'partie', 'date_echeance',
        'montant_prevu', 'reglement_recu', 'date_reglement', 'moyen_paiement',
        'montant_regle', 'notes',
    ];

    protected $casts = [
        'reglement_recu' => 'boolean',
        'date_echeance' => 'date',
        'date_reglement' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function getResteAttribute(): float
    {
        return round((float) $this->montant_prevu - (float) $this->montant_regle, 2);
    }

    public function getEstEnRetardAttribute(): bool
    {
        return ! $this->reglement_recu && now()->greaterThan($this->date_echeance);
    }
}
