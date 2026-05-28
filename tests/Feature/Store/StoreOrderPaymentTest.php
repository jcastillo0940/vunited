<?php

namespace Tests\Feature\Store;

use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentSetting;
use App\Domain\Store\Enums\StoreOrderStatus;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\StoreOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoreOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_store_order_with_active_products(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create([
            'price' => '65.00',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/store/orders', $this->validPayload($product));

        $response->assertStatus(201)
            ->assertJsonStructure(['order_number', 'status', 'approve_url', 'payment_id', 'total', 'currency']);

        $this->assertDatabaseHas('store_orders', [
            'customer_name' => 'Ana Tribu',
            'customer_email' => 'ana@example.com',
            'status' => 'pending_payment',
            'currency' => 'USD',
        ]);
    }

    public function test_calculates_total_in_backend(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create(['price' => '45.00']);

        $response = $this->postJson('/api/store/orders', array_merge(
            $this->validPayload($product, quantity: 2),
            ['total' => '1.00'],
        ));

        $response->assertStatus(201)
            ->assertJsonFragment([
                'total' => '90.00',
                'currency' => 'USD',
            ]);

        $this->assertDatabaseHas('store_orders', [
            'subtotal' => '90.00',
            'total' => '90.00',
        ]);
    }

    public function test_creates_store_order_items_with_snapshot(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create([
            'name' => 'Camiseta Local Oficial',
            'sku' => 'VU-LOCAL-2026',
            'slug' => 'camiseta-local-oficial',
            'price' => '70.00',
        ]);

        $this->postJson('/api/store/orders', $this->validPayload($product))->assertStatus(201);

        $order = StoreOrder::query()->with('items')->firstOrFail();
        $item = $order->items->first();

        $this->assertNotNull($item);
        $this->assertSame('Camiseta Local Oficial', $item->product_name);
        $this->assertSame('VU-LOCAL-2026', $item->product_sku);
        $this->assertSame('camiseta-local-oficial', $item->metadata['product_snapshot']['slug'] ?? null);
    }

    public function test_creates_associated_payment_and_returns_approve_url(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create(['price' => '52.00']);

        $response = $this->postJson('/api/store/orders', $this->validPayload($product));

        $response->assertStatus(201);
        $this->assertStringContainsString('sandbox.paypal.com', $response->json('approve_url'));

        $order = StoreOrder::query()->firstOrFail();
        $payment = Payment::query()
            ->where('payable_type', StoreOrder::class)
            ->where('payable_id', $order->id)
            ->first();

        $this->assertNotNull($payment);
        $this->assertSame(PaymentStatus::ProviderCreated, $payment->status);
        $this->assertSame('52.00', $payment->amount);
    }

    public function test_rejects_inactive_product(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create(['is_active' => false]);

        $this->postJson('/api/store/orders', $this->validPayload($product))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Uno o mas productos no estan disponibles.']);
    }

    public function test_rejects_insufficient_stock_when_tracking_stock(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create([
            'track_stock' => true,
            'stock_quantity' => 1,
        ]);

        $this->postJson('/api/store/orders', $this->validPayload($product, quantity: 2))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => "Stock insuficiente para {$product->name}."]);
    }

    public function test_webhook_captured_marks_store_order_paid_and_discounts_stock(): void
    {
        $this->makeEnabledSetting();
        $product = Product::factory()->create([
            'track_stock' => true,
            'stock_quantity' => 9,
        ]);

        $order = StoreOrder::factory()->create([
            'status' => StoreOrderStatus::PendingPayment,
            'total' => '130.00',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => '65.00',
            'quantity' => 2,
            'line_total' => '130.00',
            'metadata' => ['product_snapshot' => ['slug' => $product->slug]],
        ]);

        Payment::factory()->create([
            'payable_type' => StoreOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-STORE001',
            'status' => PaymentStatus::Approved,
            'amount' => 130.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-STORE-CAP-01',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-STORE001',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-STORE001'],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $product->refresh();

        $this->assertSame(StoreOrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(7, $product->stock_quantity);
    }

    public function test_webhook_failed_marks_store_order_failed(): void
    {
        $this->makeEnabledSetting();
        $order = StoreOrder::factory()->create(['status' => StoreOrderStatus::PendingPayment]);

        Payment::factory()->create([
            'payable_type' => StoreOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-STORE002',
            'status' => PaymentStatus::Approved,
            'amount' => 65.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-STORE-DEN-01',
            'event_type' => 'PAYMENT.CAPTURE.DENIED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-STORE002',
                'status' => 'DECLINED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-STORE002'],
                ],
            ],
        ])->assertStatus(200);

        $this->assertSame(StoreOrderStatus::Failed, $order->fresh()->status);
    }

    public function test_webhook_refunded_marks_store_order_cancelled(): void
    {
        $this->makeEnabledSetting();
        $order = StoreOrder::factory()->create(['status' => StoreOrderStatus::Paid, 'paid_at' => now()]);

        Payment::factory()->captured()->create([
            'payable_type' => StoreOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-STORE003',
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-STORE-REF-01',
            'event_type' => 'PAYMENT.CAPTURE.REFUNDED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-STORE003',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => [
                        'order_id' => 'PAYID-STORE003',
                        'capture_id' => 'CAP-STORE003',
                    ],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $this->assertSame(StoreOrderStatus::Cancelled, $order->status);
        $this->assertNotNull($order->cancelled_at);
    }

    public function test_does_not_process_card_or_cvv_fields(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();
        $product = Product::factory()->create();

        $payload = array_merge($this->validPayload($product), [
            'card_number' => '4111111111111111',
            'card_cvv' => '123',
        ]);

        $this->postJson('/api/store/orders', $payload)->assertStatus(201);

        $order = StoreOrder::query()->firstOrFail();
        $payment = Payment::query()->where('payable_type', StoreOrder::class)->firstOrFail();

        $this->assertNull($order->metadata['card_number'] ?? null);
        $this->assertNull($payment->metadata['card_cvv'] ?? null);
        $this->assertStringNotContainsString('4111111111111111', json_encode($payment->provider_payload ?? []));
    }

    public function test_ticket_orders_endpoint_still_does_not_exist(): void
    {
        $this->post('/api/ticket-orders')->assertStatus(404);
    }

    private function makeEnabledSetting(): PaymentSetting
    {
        return PaymentSetting::create([
            'provider' => 'paypal',
            'mode' => 'sandbox',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'currency' => 'USD',
            'is_enabled' => true,
            'webhook_id' => null,
        ]);
    }

    private function fakePayPalSuccess(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'fake-token',
                'token_type' => 'Bearer',
                'expires_in' => 32400,
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => 'PAYID-STORE-TEST',
                'status' => 'CREATED',
                'links' => [
                    ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYID-STORE-TEST'],
                ],
            ], 201),
        ]);
    }

    private function validPayload(Product $product, int $quantity = 1): array
    {
        return [
            'customer_name' => 'Ana Tribu',
            'customer_email' => 'ana@example.com',
            'customer_phone' => '+507 6000-0101',
            'accept_terms' => true,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ],
            ],
        ];
    }
}
