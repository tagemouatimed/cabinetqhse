<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = ['client_id', 'projet_id', 'norme_id', 'type', 'date_planifiee', 'recurrence_mois', 'statut'];

    protected $casts = ['date_planifiee' => 'date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function norme()
    {
        return $this->belongsTo(Norme::class);
    }

    public function planAudit()
    {
        return $this->hasOne(PlanAudit::class);
    }

    public function rapport()
    {
        return $this->hasOne(RapportAudit::class);
    }

    /**
     * Génère la prochaine occurrence pour un audit récurrent réalisé.
     */
    public function genererProchaineOccurrence(): ?self
    {
        if ($this->type !== 'recurrent' || ! $this->recurrence_mois) {
            return null;
        }

        return static::create([
            'client_id' => $this->client_id,
            'projet_id' => $this->projet_id,
            'norme_id' => $this->norme_id,
            'type' => 'recurrent',
            'date_planifiee' => $this->date_planifiee->copy()->addMonths($this->recurrence_mois),
            'recurrence_mois' => $this->recurrence_mois,
            'statut' => 'planifie',
        ]);
    }
}
