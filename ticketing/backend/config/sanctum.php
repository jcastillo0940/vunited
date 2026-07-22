<?php

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | No se usa: los frontends de esta app autentican con Bearer token (React
    | fetch con Authorization header), nunca con la sesion/cookie de primera
    | parte de Sanctum. Se deja vacio a proposito.
    |
    */

    'stateful' => [],

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Esta lista es solo para el fallback de sesion/cookie de primera parte
    | (Guard::__invoke la recorre ANTES de mirar el bearer token). Esta app
    | no usa sesiones - vacio a proposito. OJO: nunca poner aqui un guard
    | driver=sanctum (el propio 'sanctum' o 'sanctum_customers'): Guard
    | volveria a resolverse a si mismo y produce recursion infinita (ya
    | paso una vez desplegando esto - ver commit que corrigio esto).
    |
    | La validacion real de que un token de Customer no se cuele por el
    | guard de Operator (o viceversa) NO pasa por aqui: cada guard sanctum
    | (uno por 'provider' distinto, ver config/auth.php) valida el
    | tokenable contra su propio provider en Guard::hasValidProvider().
    |
    */

    'guard' => [],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],

];
