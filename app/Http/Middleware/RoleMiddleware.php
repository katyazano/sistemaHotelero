<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Permite uno o varios roles separados por coma.
     * Ej: ->middleware('role:admin,personal')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'Acceso no autorizado.');
        }

        // Permite tanto 'role:admin,personal' como 'role:admin|personal'
        $rolesPermitidos = collect($roles)
            ->flatMap(fn ($r) => preg_split('/[|,]/', $r))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->all();

        if (!in_array(Auth::user()->rol, $rolesPermitidos, true)) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
