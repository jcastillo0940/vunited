<?php

namespace App\Domain\Wallets\Services;

use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;

/**
 * Construye el pass.json de un Apple Wallet event ticket y lo firma como
 * .pkpass (zip con manifest.json + signature PKCS#7) si hay certificado
 * Pass Type ID configurado. Sin certificado real (no lo hay en este
 * servidor - ver docs/architecture/wallets.md) construye igual el pass.json
 * para poder probarlo, pero lanza WalletNotConfiguredException al intentar
 * firmar/empaquetar, en vez de generar un .pkpass invalido que Apple
 * rechazaria silenciosamente.
 */
class AppleWalletService
{
    public function __construct(
        private readonly ?string $passTypeId,
        private readonly ?string $teamId,
        private readonly ?string $certPath,
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->passTypeId) && ! empty($this->teamId) && ! empty($this->certPath) && is_readable((string) $this->certPath);
    }

    /**
     * @return array<string, mixed> el contenido de pass.json (sin firmar)
     */
    public function buildPassPayload(Ticket $ticket): array
    {
        $event = $ticket->event;

        return [
            'formatVersion' => 1,
            'passTypeIdentifier' => $this->passTypeId ?? 'pending.configuration',
            'teamIdentifier' => $this->teamId ?? 'pending.configuration',
            'serialNumber' => $ticket->public_id,
            'organizationName' => 'Veraguas United FC',
            'description' => 'Boleto '.($event?->home_team).' vs '.($event?->away_team),
            'eventTicket' => [
                'primaryFields' => [
                    ['key' => 'event', 'label' => 'PARTIDO', 'value' => trim(($event?->home_team ?? '').' vs '.($event?->away_team ?? ''))],
                ],
                'secondaryFields' => [
                    ['key' => 'zone', 'label' => 'ZONA', 'value' => $ticket->zone?->name],
                    ['key' => 'seat', 'label' => 'ASIENTO', 'value' => $ticket->seat?->label ?? 'General'],
                ],
                'auxiliaryFields' => [
                    ['key' => 'date', 'label' => 'FECHA', 'value' => $event?->starts_at?->toIso8601String()],
                ],
            ],
            'barcodes' => [[
                'format' => 'PKBarcodeFormatQR',
                // Mismo token firmado del QR fisico - nunca datos personales.
                'message' => $ticket->qr_token,
                'messageEncoding' => 'iso-8859-1',
            ]],
        ];
    }

    /**
     * @throws WalletNotConfiguredException
     */
    public function buildSignedPkpass(Ticket $ticket): string
    {
        if (! $this->isConfigured()) {
            throw new WalletNotConfiguredException(
                'Apple Wallet no esta configurado (APPLE_WALLET_PASS_TYPE_ID / APPLE_WALLET_TEAM_ID / APPLE_WALLET_CERT_PATH). '.
                'Requiere un certificado Pass Type ID emitido desde una cuenta Apple Developer.',
            );
        }

        // La firma real (openssl_pkcs7_sign sobre manifest.json + empaquetado
        // zip con pass.json/manifest.json/signature/iconos) se implementa
        // aqui una vez exista el certificado - ver docs/architecture/wallets.md.
        throw new WalletNotConfiguredException('Firma de .pkpass pendiente de certificado real.');
    }
}
