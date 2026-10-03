<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;

/**
 * Écart documenté par rapport au principe qcore "1 middleware par périmètre de rôle" :
 * la matrice de permissions est ici granulaire (module x action), un middleware par rôle
 * dupliquerait la même logique pour chaque rôle. Un middleware générique paramétré
 * (ex: 'permission:facturation,ajouter') consulte la table `permissions` à la place.
 *
 * Usage dans routes/web.php : ->middleware('permission:facturation,ajouter')
 */
class VerifierPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action)
    {
        $role = $request->user()?->role ?? 'invite';

        if (! Permission::autorise($role, $module, $action)) {
            abort(403, "Accès non autorisé pour le module {$module}.");
        }

        return $next($request);
    }
}
