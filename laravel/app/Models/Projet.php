<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = ['client_id', 'nom', 'norme', 'secteur', 'date_debut', 'date_fin_prevue', 'statut', 'avancement'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plansAction()
    {
        return $this->hasMany(PlanAction::class);
    }

    public function notes()
    {
        return $this->hasMany(NoteProjet::class);
    }

    public function rapportsReunion()
    {
        return $this->hasMany(RapportReunion::class);
    }

    public function pieceJointes()
    {
        return $this->morphMany(PieceJointe::class, 'attachable');
    }

    /**
     * Recalcule et enregistre l'avancement (%) à partir des actions de tous les plans du projet.
     */
    public function recalculerAvancement(): void
    {
        $actions = Action::whereIn('plan_action_id', $this->plansAction()->pluck('id'));
        $total = $actions->count();
        $realisees = (clone $actions)->where('statut', 'realisee')->count();

        $this->update(['avancement' => $total ? (int) round($realisees / $total * 100) : 0]);
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_fin_prevue
            && now()->greaterThan($this->date_fin_prevue)
            && $this->statut !== 'termine';
    }
}
