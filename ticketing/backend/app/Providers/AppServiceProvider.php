<?php

namespace App\Providers;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Payments\Gateways\HttpPaymentsGateway;
use App\Domain\Ticketing\Support\TicketQrSigner;
use App\Domain\Wallets\Services\AppleWalletService;
use App\Domain\Wallets\Services\GoogleWalletService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentsGateway::class, HttpPaymentsGateway::class);

        $this->app->singleton(TicketQrSigner::class, function () {
            $key = config('services.ticket_qr.signing_key');
            if (empty($key)) {
                throw new \RuntimeException('TICKET_QR_SIGNING_KEY no esta configurada.');
            }

            return new TicketQrSigner($key);
        });

        $this->app->singleton(GoogleWalletService::class, fn () => new GoogleWalletService(
            config('services.google_wallet.issuer_id'),
            config('services.google_wallet.service_account_json'),
        ));

        $this->app->singleton(AppleWalletService::class, fn () => new AppleWalletService(
            config('services.apple_wallet.pass_type_id'),
            config('services.apple_wallet.team_id'),
            config('services.apple_wallet.cert_path'),
        ));
    }

    public function boot(): void
    {
        //
    }
}
