<?php

namespace App\Domain\Wallets\Services;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;

/**
 * Genera el enlace "Guardar en Google Wallet" firmando un JWT RS256 con la
 * cuenta de servicio de Google Wallet (no requiere una libreria de JWT
 * externa: RS256 es solo openssl_sign con SHA256).
 *
 * La clase del pase (EventTicketClass) va embebida en el mismo JWT junto al
 * objeto (payload.eventTicketClasses + payload.eventTicketObjects). Google
 * la inserta o actualiza (upsert) la primera vez que alguien toca "Guardar",
 * asi que no hace falta llamar a la Wallet REST API por separado ni pedir un
 * access token OAuth2 - un partido nuevo en la tabla `events` genera su
 * propia clase automaticamente, sin tocar la consola de Google a mano.
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
        private readonly ?string $issuerName = null,
        private readonly ?string $logoUrl = null,
        private readonly ?string $heroImageUrl = null,
        private readonly string $hexBackgroundColor = '#004AAD',
        private readonly string $reviewStatus = 'UNDER_REVIEW',
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

        if (empty($this->issuerName) || empty($this->logoUrl)) {
            throw new WalletNotConfiguredException(
                'Falta el branding de Google Wallet (GOOGLE_WALLET_ISSUER_NAME / GOOGLE_WALLET_LOGO_URL).',
            );
        }

        $event = $ticket->event;
        if (! $event) {
            throw new WalletNotConfiguredException('El boleto no tiene un evento asociado.');
        }

        $account = json_decode($this->serviceAccountJson, true);
        if (! is_array($account) || empty($account['client_email']) || empty($account['private_key'])) {
            throw new WalletNotConfiguredException('GOOGLE_WALLET_SERVICE_ACCOUNT_JSON invalido.');
        }

        $classId = "{$this->issuerId}.event-{$event->public_id}";
        $objectId = "{$this->issuerId}.{$ticket->public_id}";

        $payload = [
            'iss' => $account['client_email'],
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'origins' => [config('app.url')],
            'payload' => [
                'eventTicketClasses' => [$this->buildEventTicketClass($event, $classId)],
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

    /**
     * Define la clase del pase para el partido: estadio, direccion y fecha
     * salen del propio modelo Event (`venue_name`, `venue_location`,
     * `starts_at`), asi que un partido nuevo no requiere editar nada en la
     * consola de Google - el proximo "Guardar en Wallet" crea su clase sola.
     *
     * @return array<string, mixed>
     */
    private function buildEventTicketClass(Event $event, string $classId): array
    {
        $class = [
            'id' => $classId,
            'issuerName' => $this->issuerName,
            'reviewStatus' => $this->reviewStatus,
            'hexBackgroundColor' => $this->hexBackgroundColor,
            'eventName' => $this->localizedValue(trim("{$event->home_team} vs {$event->away_team}")),
            'logo' => [
                'sourceUri' => ['uri' => $this->logoUrl],
                'contentDescription' => $this->localizedValue('Veraguas United FC'),
            ],
        ];

        if ($event->venue_name || $event->venue_location) {
            $class['venue'] = [
                'name' => $this->localizedValue($event->venue_name ?? ''),
                'address' => $this->localizedValue($event->venue_location ?? ''),
            ];
        }

        if ($event->starts_at) {
            $class['dateTime'] = ['start' => $event->starts_at->toIso8601String()];
        }

        if (! empty($this->heroImageUrl)) {
            $class['heroImage'] = [
                'sourceUri' => ['uri' => $this->heroImageUrl],
                'contentDescription' => $this->localizedValue(trim("{$event->home_team} vs {$event->away_team}")),
            ];
        }

        return $class;
    }

    /**
     * @return array<string, mixed>
     */
    private function localizedValue(string $value): array
    {
        return ['defaultValue' => ['language' => 'es', 'value' => $value]];
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
