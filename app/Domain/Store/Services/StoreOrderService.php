<?php

namespace App\Domain\Store\Services;

use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Providers\PayPalPaymentProvider;
use App\Domain\Payments\Services\PaymentLifecycleService;
use App\Domain\Store\Enums\StoreOrderStatus;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\StoreOrder;
use App\Domain\Store\Models\StoreOrderItem;
use Illuminate\Support\Facades\DB;

class StoreOrderService
{
    public function __construct(
        private readonly PaymentLifecycleService $lifecycle,
        private readonly PayPalPaymentProvider $provider,
    ) {}

    public function createOrder(array $data): array
    {
        $products = Product::query()
            ->with('category')
            ->whereIn('id', collect($data['items'])->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        $orderNumber = $this->generateOrderNumber();

        $order = DB::transaction(function () use ($data, $products, $orderNumber): StoreOrder {
            $subtotal = 0;
            $itemsPayload = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active || ($product->category && ! $product->category->is_active)) {
                    throw new PaymentProviderException('Uno o mas productos no estan disponibles.');
                }

                if ($product->track_stock && (int) ($product->stock_quantity ?? 0) < (int) $item['quantity']) {
                    throw new PaymentProviderException("Stock insuficiente para {$product->name}.");
                }

                $lineTotal = round((float) $product->price * (int) $item['quantity'], 2);
                $subtotal += $lineTotal;

                $itemsPayload[] = [
                    'product' => $product,
                    'quantity' => (int) $item['quantity'],
                    'line_total' => $lineTotal,
                    'snapshot' => [
                        'product_id' => $product->id,
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'currency' => $product->currency,
                        'badge' => $product->badge,
                        'image_path' => $product->image_path,
                        'track_stock' => $product->track_stock,
                    ],
                ];
            }

            $currency = (string) ($itemsPayload[0]['product']->currency ?? 'USD');
            $discountTotal = 0.0;
            $taxTotal = 0.0;
            $total = round($subtotal - $discountTotal + $taxTotal, 2);

            $order = StoreOrder::query()->create([
                'order_number' => $orderNumber,
                'status' => StoreOrderStatus::PendingPayment,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'discount_total' => number_format($discountTotal, 2, '.', ''),
                'tax_total' => number_format($taxTotal, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
                'currency' => $currency,
                'metadata' => [
                    'coupon_code' => $data['coupon_code'] ?? null,
                    'checkout_note' => 'Cupones reales aun no estan activos.',
                ],
            ]);

            foreach ($itemsPayload as $itemPayload) {
                /** @var Product $product */
                $product = $itemPayload['product'];

                StoreOrderItem::query()->create([
                    'store_order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $itemPayload['quantity'],
                    'line_total' => number_format($itemPayload['line_total'], 2, '.', ''),
                    'metadata' => [
                        'product_snapshot' => $itemPayload['snapshot'],
                    ],
                ]);
            }

            return $order->load('items');
        });

        $returnUrl = url('/orden-tienda-confirmada') . '?order=' . $order->order_number;

        $payment = $this->lifecycle->createPendingPayment([
            'payable_type' => StoreOrder::class,
            'payable_id' => $order->id,
            'provider' => 'paypal',
            'currency' => $order->currency,
            'amount' => $order->total,
            'description' => "Orden Tienda Veraguas United - {$order->order_number}",
            'customer_email' => $order->customer_email,
            'customer_name' => $order->customer_name,
            'metadata' => [
                'paypal_return_url' => $returnUrl,
                'paypal_cancel_url' => url('/carrito') . '?cancelled=1&order=' . $order->order_number,
                'store_order_number' => $order->order_number,
            ],
        ]);

        $result = $this->provider->createOrder($payment);

        if (! $result->success) {
            $this->lifecycle->markFailed($payment, $result->message ?? 'PayPal order creation failed.');
            $order->update(['status' => StoreOrderStatus::Failed]);

            throw new PaymentProviderException(
                $result->message ?? 'No se pudo crear la orden de tienda en PayPal.',
            );
        }

        $this->lifecycle->markProviderCreated($payment, $result);

        return [
            'order' => $order->refresh()->load(['items', 'payment']),
            'payment' => $payment->refresh(),
            'approve_url' => $result->redirectUrl,
        ];
    }

    private function generateOrderNumber(): string
    {
        $year = now()->year;
        $count = StoreOrder::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('STORE-%d-%04d', $year, $count);
    }
}
