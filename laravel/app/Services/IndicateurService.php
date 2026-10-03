<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\Projet;
use Illuminate\Support\Carbon;

/**
 * Requêtes prédéfinies utilisables par un indicateur "source=base" (cle_base).
 * Volontairement fermé (whitelist) : l'utilisateur choisit une clé dans une liste,
 * il ne saisit jamais de SQL libre — cf. §2 des règles de structure du rapport LabSysPro/qcore
 * (pas de saisie non contrôlée pouvant toucher la base).
 */
class IndicateurService
{
    public static function cles(): array
    {
        return [
            'ca_mensuel' => "Chiffre d'affaires du mois en cours",
            'ca_annuel' => "Chiffre d'affaires de l'année en cours",
            'retard_accompagnement_global' => "Nombre de projets en retard (tous)",
        ];
    }

    public static function calculer(string $cle, ?string $periode = null): ?float
    {
        return match ($cle) {
            'ca_mensuel' => (float) Facture::whereBetween('date_facture', [now()->startOfMonth(), now()->endOfMonth()])
                ->where('est_avoir', false)->sum('montant_ttc'),
            'ca_annuel' => (float) Facture::whereYear('date_facture', now()->year)
                ->where('est_avoir', false)->sum('montant_ttc'),
            'retard_accompagnement_global' => (float) Projet::get()->filter(fn ($p) => $p->est_en_retard)->count(),
            default => null,
        };
    }
}
