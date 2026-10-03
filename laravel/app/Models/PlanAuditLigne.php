<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAuditLigne extends Model
{
    protected $table = 'plan_audit_lignes';
    protected $fillable = ['plan_audit_id', 'processus_audite', 'duree_minutes', 'auditeur', 'audite', 'date_prevue'];
    protected $casts = ['date_prevue' => 'datetime'];

    public function planAudit()
    {
        return $this->belongsTo(PlanAudit::class);
    }
}
