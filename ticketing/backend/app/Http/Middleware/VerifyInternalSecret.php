<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege las rutas /internal/*: solo Payments (u otro servicio interno)
 * puede llamarlas, nunca el navegador. El secreto rota via shared/.env de
 * cada servicio (Fase 2), nunca se hardcodea ni se loguea.
 */
class VerifyInternalSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.payments.internal_secret');
        $provided = $request->bearerToken();

        if (empty($expected) || ! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
