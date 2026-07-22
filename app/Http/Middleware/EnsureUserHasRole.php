<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint une route aux rôles listés : ->middleware('role:administration,direction').
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user && $user->is_active, 403);

        $allowed = array_map(fn (string $role) => UserRole::from($role), $roles);

        abort_unless($user->hasRole(...$allowed), 403, 'Vous n\'avez pas accès à cette section.');

        return $next($request);
    }
}
