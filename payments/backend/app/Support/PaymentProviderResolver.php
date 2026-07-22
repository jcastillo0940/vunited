<?php
namespace App\Support; use App\Contracts\PaymentProvider; use App\Services\CashProvider; use App\Services\TilopayProvider;
class PaymentProviderResolver { public static function resolve(string $provider): PaymentProvider { return match ($provider) { 'cash' => app(CashProvider::class), default => app(TilopayProvider::class) }; } }
