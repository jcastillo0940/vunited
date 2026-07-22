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
    | No se usa: el panel admin autentica con Bearer token (React fetch con
    | Authorization header), nunca con la sesion/cookie de primera parte de
    | Sanctum. Se deja vacio a proposito.
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
    | no usa sesiones - vacio a proposito. OJO: nunca poner aqui el propio
    | guard 'sanctum' (driver=sanctum): Guard volveria a resolverse a si
    | mismo -> recursion infinita (500 "Maximum call stack size").
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
