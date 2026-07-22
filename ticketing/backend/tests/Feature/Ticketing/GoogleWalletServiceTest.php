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

    private function ticket(array $eventAttributes = []): Ticket
    {
        $event = Event::create(array_merge([
            'code' => 'wallet-'.uniqid(),
            'home_team' => 'A',
            'away_team' => 'B',
            'starts_at' => now()->addDays(2),
            'venue_name' => 'Estadio Rommel Fernandez',
            'venue_location' => 'Av. Justo Arosemena, Panama',
        ], $eventAttributes));
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

    public function test_throws_clearly_when_branding_missing(): void
    {
        $service = new GoogleWalletService('3388000000000000000', '{"client_email":"a@b.com","private_key":"x"}');
        $this->expectException(WalletNotConfiguredException::class);
        $this->expectExceptionMessage('branding');
        $service->buildSaveLink($this->ticket());
    }

    private function keyPair(): array
    {
        $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($res, $privateKeyPem);
        $publicKeyPem = openssl_pkey_get_details($res)['key'];

        return [$privateKeyPem, $publicKeyPem];
    }

    public function test_builds_a_verifiable_rs256_jwt_save_link_with_event_class_embedded(): void
    {
        // Par de llaves de PRUEBA generado aqui mismo, no son credenciales
        // reales de Google - sirven solo para probar que la firma RS256
        // que produce el servicio es matematicamente correcta.
        [$privateKeyPem, $publicKeyPem] = $this->keyPair();

        $serviceAccountJson = json_encode([
            'client_email' => 'test-wallet@example.iam.gserviceaccount.com',
            'private_key' => $privateKeyPem,
        ]);

        $service = new GoogleWalletService(
            issuerId: '3388000000000000000',
            serviceAccountJson: $serviceAccountJson,
            issuerName: 'Veraguas United FC',
            logoUrl: 'https://example.com/logo.png',
            heroImageUrl: 'https://example.com/hero.png',
            hexBackgroundColor: '#004AAD',
            reviewStatus: 'UNDER_REVIEW',
        );
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

        $object = $payload['payload']['eventTicketObjects'][0];
        $this->assertSame('signed-token-example', $object['barcode']['value']);
        $this->assertStringNotContainsString('@example.com', json_encode($object['barcode']));

        $class = $payload['payload']['eventTicketClasses'][0];
        $this->assertSame($object['classId'], $class['id']);
        $this->assertSame("3388000000000000000.event-{$ticket->event->public_id}", $class['id']);
        $this->assertSame('Veraguas United FC', $class['issuerName']);
        $this->assertSame('UNDER_REVIEW', $class['reviewStatus']);
        $this->assertSame('#004AAD', $class['hexBackgroundColor']);
        $this->assertSame('Estadio Rommel Fernandez', $class['venue']['name']['defaultValue']['value']);
        $this->assertSame('Av. Justo Arosemena, Panama', $class['venue']['address']['defaultValue']['value']);
        $this->assertSame('https://example.com/logo.png', $class['logo']['sourceUri']['uri']);
        $this->assertSame('https://example.com/hero.png', $class['heroImage']['sourceUri']['uri']);
        $this->assertArrayHasKey('dateTime', $class);
    }

    public function test_each_event_gets_its_own_class_id(): void
    {
        [$privateKeyPem] = $this->keyPair();
        $serviceAccountJson = json_encode([
            'client_email' => 'test-wallet@example.iam.gserviceaccount.com',
            'private_key' => $privateKeyPem,
        ]);

        $service = new GoogleWalletService(
            issuerId: '3388000000000000000',
            serviceAccountJson: $serviceAccountJson,
            issuerName: 'Veraguas United FC',
            logoUrl: 'https://example.com/logo.png',
        );

        $ticketA = $this->ticket(['code' => 'match-a-'.uniqid(), 'home_team' => 'Veraguas United', 'away_team' => 'Independiente']);
        $ticketB = $this->ticket(['code' => 'match-b-'.uniqid(), 'home_team' => 'Veraguas United', 'away_team' => 'Tauro']);

        $decode = function (string $link) {
            $jwt = substr($link, strlen('https://pay.google.com/gp/v/save/'));
            [, $bodyB64] = explode('.', $jwt);

            return json_decode(base64_decode(strtr($bodyB64, '-_', '+/')), true);
        };

        $payloadA = $decode($service->buildSaveLink($ticketA));
        $payloadB = $decode($service->buildSaveLink($ticketB));

        $this->assertNotSame(
            $payloadA['payload']['eventTicketClasses'][0]['id'],
            $payloadB['payload']['eventTicketClasses'][0]['id'],
        );
    }
}
