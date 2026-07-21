<?php

namespace App\Domain\Wallets\Services;

use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;

/**
 * Genera el enlace "Guardar en Google Wallet" firmando un JWT RS256 con la
 * cuenta de servicio de Google Wallet (no requiere una libreria de JWT
 * externa: RS256 es solo openssl_sign con SHA256).
 *
 * El codigo de barras del pase usa el mismo qr_token firmado que ya se
 * imprime/muestra en el ticket - nunca se genera un identificador nuevo
 * para el wallet, evitando dos fuentes de verdad para "que boleto es este".
 *
 * Sin credenciales reales de Google Wallet configuradas (issuer id +
 * cuenta de servicio), lanza WalletNotConfiguredException de forma clara
 * en vez de fallar a medias. Ver docs/architecture/wallets.md.
 */
class GoogleWalletService
{
    public function __construct(
        private readonly ?string $issuerId,
        private readonly ?string $serviceAccountJson,
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->issuerId) && ! empty($this->serviceAccountJson);
    }

    /**
     * @throws WalletNotConfiguredException
     */
    public function buildSaveLink(Ticket $ticket): string
    {
        if (! $this->isConfigured()) {
            throw new WalletNotConfiguredException(
                'Google Wallet no esta configurado (GOOGLE_WALLET_ISSUER_ID / GOOGLE_WALLET_SERVICE_ACCOUNT_JSON).',
            );
        }

        $account = json_decode($this->serviceAccountJson, true);
        if (! is_array($account) || empty($account['client_email']) || empty($account['private_key'])) {
            throw new WalletNotConfiguredException('GOOGLE_WALLET_SERVICE_ACCOUNT_JSON invalido.');
        }

        $classId = "{$this->issuerId}.veraguas-ticketing-event";
        $objectId = "{$this->issuerId}.{$ticket->public_id}";

        $payload = [
            'iss' => $account['client_email'],
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'origins' => [config('app.url')],
            'payload' => [
                'eventTicketObjects' => [[
                    'id' => $objectId,
                    'classId' => $classId,
                    'state' => 'ACTIVE',
                    'ticketHolderName' => $ticket->order?->customer_name,
                    'seatInfo' => [
                        'seat' => ['defaultValue' => ['language' => 'es', 'value' => $ticket->seat?->label ?? $ticket->zone?->name]],
                    ],
                    'barcode' => [
                        'type' => 'QR_CODE',
                        // El valor escaneable es SIEMPRE el token firmado, sin PII.
                        'value' => $ticket->qr_token,
                    ],
                ]],
            ],
        ];

        $jwt = $this->signRs256($payload, $account['private_key']);

        return "https://pay.google.com/gp/v/save/{$jwt}";
    }

    private function signRs256(array $payload, string $privateKeyPem): string
    {
        $header = self::b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_UNESCAPED_SLASHES));
        $body = self::b64(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signingInput = "{$header}.{$body}";

        $privateKey = openssl_pkey_get_private($privateKeyPem);
        if ($privateKey === false) {
            throw new WalletNotConfiguredException('Llave privada de Google Wallet invalida.');
        }

        openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return "{$signingInput}.".self::b64($signature);
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
