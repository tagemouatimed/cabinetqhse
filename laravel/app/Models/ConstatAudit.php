<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConstatAudit extends Model
{
    protected $table = 'constats_audit';
    protected $fillable = ['rapport_audit_id', 'type', 'reference_norme', 'description', 'action_corrective_id'];

    public function rapport()
    {
        return $this->belongsTo(RapportAudit::class, 'rapport_audit_id');
    }

    public function actionCorrective()
    {
        return $this->belongsTo(Action::class, 'action_corrective_id');
    }
}
