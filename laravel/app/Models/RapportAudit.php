<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportAudit extends Model
{
    protected $table = 'rapports_audit';
    protected $fillable = ['audit_id', 'statut', 'score'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function constats()
    {
        return $this->hasMany(ConstatAudit::class);
    }

    /**
     * Score simple : 100 - pénalités (écart majeur -15, écart mineur -5, point sensible -2).
     */
    public function calculerScore(): int
    {
        $penalites = ['point_sensible' => 2, 'ecart_mineur' => 5, 'ecart_majeur' => 15, 'recommandation' => 0];
        $total = $this->constats->sum(fn ($c) => $penalites[$c->type] ?? 0);

        return max(0, 100 - $total);
    }
}
