<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\OrderItem;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;
use App\Domain\Wallets\Services\AppleWalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppleWalletServiceTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(): Ticket
    {
        $event = Event::create(['code' => 'apple-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(2)]);
        $zone = Zone::create(['event_id' => $event->id, 'name' => 'General', 'slug' => 'general', 'kind' => 'general', 'price' => 5, 'capacity_total' => 10, 'capacity_available' => 9]);
        $order = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'a@example.com']);
        $item = OrderItem::create(['order_id' => $order->id, 'zone_id' => $zone->id, 'quantity' => 1, 'unit_price' => 5, 'line_total' => 5]);

        return Ticket::create([
            'order_id' => $order->id, 'order_item_id' => $item->id, 'event_id' => $event->id,
            'zone_id' => $zone->id, 'status' => 'issued', 'qr_token' => 'signed-token-xyz', 'issued_at' => now(),
        ]);
    }

    public function test_pass_payload_has_no_pii_in_barcode(): void
    {
        $service = new AppleWalletService(null, null, null);
        $payload = $service->buildPassPayload($this->ticket());

        $this->assertSame('signed-token-xyz', $payload['barcodes'][0]['message']);
        $this->assertStringNotContainsString('example.com', json_encode($payload['barcodes']));
    }

    public function test_signing_throws_clearly_without_certificate(): void
    {
        $service = new AppleWalletService(null, null, null);
        $this->expectException(WalletNotConfiguredException::class);
        $service->buildSignedPkpass($this->ticket());
    }
}
