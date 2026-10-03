<?php

namespace App\Helpers;

class MontantEnLettres
{
    /**
     * Convertit un montant en toutes lettres (français), ex: 10800.00 -> "dix mille huit cents dirhams".
     * Utilise l'extension PHP intl (NumberFormatter) si disponible, sinon retombe sur le chiffre.
     */
    public static function convertir(float $montant, string $devise = 'dirhams'): string
    {
        $entier = (int) floor($montant);
        $centimes = (int) round(($montant - $entier) * 100);

        $texte = self::spellOut($entier) . ' ' . $devise;

        if ($centimes > 0) {
            $texte .= ' et ' . self::spellOut($centimes) . ' centimes';
        }

        return ucfirst($texte);
    }

    protected static function spellOut(int $nombre): string
    {
        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
            $resultat = $formatter->format($nombre);
            if ($resultat !== false) {
                return $resultat;
            }
        }

        // Repli si l'extension intl n'est pas installée sur le serveur
        return (string) $nombre;
    }
}
