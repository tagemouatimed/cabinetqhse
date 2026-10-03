<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAudit extends Model
{
    protected $table = 'plans_audit';
    protected $fillable = ['norme_id', 'audit_id', 'nom', 'est_modele'];
    protected $casts = ['est_modele' => 'boolean'];

    public function norme()
    {
        return $this->belongsTo(Norme::class);
    }

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function lignes()
    {
        return $this->hasMany(PlanAuditLigne::class);
    }

    /**
     * Duplique ce plan (modèle standard) pour l'adapter à un audit précis.
     */
    public function dupliquerPour(Audit $audit): self
    {
        $copie = static::create([
            'norme_id' => $this->norme_id,
            'audit_id' => $audit->id,
            'nom' => $this->nom,
            'est_modele' => false,
        ]);

        foreach ($this->lignes as $ligne) {
            $copie->lignes()->create($ligne->only(['processus_audite', 'duree_minutes', 'auditeur', 'audite', 'date_prevue']));
        }

        return $copie;
    }
}
