<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\OrderItem;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;
use App\Domain\Wallets\Services\GoogleWalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleWalletServiceTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(): Ticket
    {
        $event = Event::create(['code' => 'wallet-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(2)]);
        $order = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'a@example.com', 'customer_name' => 'Ana Perez']);
        $item = OrderItem::create(['order_id' => $order->id, 'zone_id' => Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general', 'kind' => 'general',
            'price' => 5, 'capacity_total' => 10, 'capacity_available' => 9,
        ])->id, 'quantity' => 1, 'unit_price' => 5, 'line_total' => 5]);

        return Ticket::create([
            'order_id' => $order->id, 'order_item_id' => $item->id, 'event_id' => $event->id,
            'zone_id' => $item->zone_id, 'status' => 'issued', 'qr_token' => 'signed-token-example', 'issued_at' => now(),
        ]);
    }

    public function test_throws_clearly_when_not_configured(): void
    {
        $service = new GoogleWalletService(null, null);
        $this->expectException(WalletNotConfiguredException::class);
        $service->buildSaveLink($this->ticket());
    }

    public function test_builds_a_verifiable_rs256_jwt_save_link(): void
    {
        // Par de llaves de PRUEBA generado aqui mismo, no son credenciales
        // reales de Google - sirven solo para probar que la firma RS256
        // que produce el servicio es matematicamente correcta.
        $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($res, $privateKeyPem);
        $publicKeyPem = openssl_pkey_get_details($res)['key'];

        $serviceAccountJson = json_encode([
            'client_email' => 'test-wallet@example.iam.gserviceaccount.com',
            'private_key' => $privateKeyPem,
        ]);

        $service = new GoogleWalletService('3388000000000000000', $serviceAccountJson);
        $ticket = $this->ticket();

        $link = $service->buildSaveLink($ticket);

        $this->assertStringStartsWith('https://pay.google.com/gp/v/save/', $link);
        $jwt = substr($link, strlen('https://pay.google.com/gp/v/save/'));

        [$headerB64, $bodyB64, $sigB64] = explode('.', $jwt);
        $signingInput = "{$headerB64}.{$bodyB64}";
        $signature = base64_decode(strtr($sigB64, '-_', '+/'));

        $valid = openssl_verify($signingInput, $signature, $publicKeyPem, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $valid, 'La firma RS256 del JWT debe verificar correctamente con la llave publica correspondiente.');

        $payload = json_decode(base64_decode(strtr($bodyB64, '-_', '+/')), true);
        $this->assertSame('signed-token-example', $payload['payload']['eventTicketObjects'][0]['barcode']['value']);
        $this->assertStringNotContainsString('@example.com', json_encode($payload['payload']['eventTicketObjects'][0]['barcode']));
    }
}
