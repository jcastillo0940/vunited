<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAudience
{
    public function handle(Request $request, Closure $next, string $audience): Response
    {
        abort_unless($request->user() && $request->user()->currentAccessToken()?->can('audience:'.$audience), 403, 'Audiencia no autorizada.');
        return $next($request);
    }
}
