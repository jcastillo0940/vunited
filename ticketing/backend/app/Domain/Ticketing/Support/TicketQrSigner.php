<?php

namespace App\Domain\Ticketing\Support;

use App\Domain\Ticketing\Models\Ticket;

/**
 * Firma y verifica el token opaco que va dentro del QR. El payload SOLO
 * contiene el public_id del ticket y el id del evento - nunca nombre,
 * correo, telefono ni precio (Fase 7 §7). La firma HMAC permite detectar
 * alteracion y, a diferencia de un token puramente aleatorio, se puede
 * verificar sin conexion (offline) porque no depende de una consulta a la
 * base para confirmar autenticidad - solo el ESTADO (usado/revocado) si
 * requiere red. Ver docs/architecture/ticketing-contingency.md.
 */
class TicketQrSigner
{
    public function __construct(private readonly string $key) {}

    public function sign(Ticket $ticket): string
    {
        $payload = $ticket->public_id.'|'.$ticket->event_id;
        $signature = hash_hmac('sha256', $payload, $this->key);

        return self::encode($payload).'.'.self::encode($signature);
    }

    /**
     * @return array{ticket_public_id: string, event_id: int}|null null si el
     *                                                             token esta mal formado o la firma no coincide (alterado).
     */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$payloadEncoded, $signatureEncoded] = $parts;
        $payload = self::decode($payloadEncoded);
        $signature = self::decode($signatureEncoded);

        if ($payload === null || $signature === null) {
            return null;
        }

        $expected = hash_hmac('sha256', $payload, $this->key);

        // hash_equals: comparacion en tiempo constante, evita timing attacks
        // que permitirian adivinar la firma byte a byte.
        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $segments = explode('|', $payload, 2);
        if (count($segments) !== 2) {
            return null;
        }

        return [
            'ticket_public_id' => $segments[0],
            'event_id' => (int) $segments[1],
        ];
    }

    private static function encode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function decode(string $encoded): ?string
    {
        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
