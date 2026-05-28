<?php

use Illuminate\Support\Str;

return [

    'paypal' => [
        'sandbox_base_url' => 'https://api-m.sandbox.paypal.com',
        'live_base_url'    => 'https://api-m.paypal.com',
        'return_url'       => env(
            'PAYPAL_RETURN_URL',
            Str::of(env('APP_URL', 'http://localhost'))->rtrim('/')->append('/registro-confirmado')->toString(),
        ),
        'cancel_url'       => env(
            'PAYPAL_CANCEL_URL',
            Str::of(env('APP_URL', 'http://localhost'))->rtrim('/')->append('/registro-tribu')->toString(),
        ),
    ],

];
