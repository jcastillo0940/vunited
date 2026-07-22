<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Payments\Data\PaymentIntentResult;
use App\Domain\Payments\Data\RefundResult;
use App\Domain\Ticketing\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implementacion real del contrato: llama al servicio interno de Payments
 * por HTTP con un secreto rotable (nunca credenciales de TiloPay). Mientras
 * Payments no exista como servicio propio (pendiente de otra fase), esto
 * fallará de forma controlada (success=false) en vez de tumbar el flujo de
 * Ticketing - ver docs/architecture/payments-integration.md.
 */
class HttpPaymentsGateway implements PaymentsGateway
{
    public function createIntent(Order $order, string $method = 'tilopay'): PaymentIntentResult
    {
        if ($method === 'cash') {
            return $this->createCashIntent($order);
        }

        try {
            $response = Http::withToken(config('services.payments.internal_secret'))
                ->timeout(5)
                ->post(config('services.payments.base_url').'/intents', [
                    'idempotency_key' => 'ticketing-order-'.$order->public_id,
                    'domain' => 'ticketing',
                    'reference' => $order->order_number,
                    'amount' => (float) $order->total,
                    'currency' => $order->currency,
                    'customer_email' => $order->customer_email,
                    'return_url' => config('app.url').'/ordenes/'.$order->public_id,
                ]);

            if (! $response->successful()) {
                Log::warning('payments.create_intent_failed', [
                    'order' => $order->public_id,
                    'status' => $response->status(),
                ]);

                return new PaymentIntentResult(success: false, errorMessage: 'Payments respondio '.$response->status());
            }

            $data = $response->json();

            return new PaymentIntentResult(
                success: true,
                intentId: $data['intent_id'] ?? null,
                redirectUrl: $data['redirect_url'] ?? null,
            );
        } catch (\Throwable $e) {
            Log::error('payments.create_intent_exception', [
                'order' => $order->public_id,
                'error' => $e->getMessage(),
            ]);

            return new PaymentIntentResult(success: false, errorMessage: 'No se pudo contactar a Payments.');
        }
    }

    /**
     * Ruta de efectivo: llama al endpoint real de Payments (a diferencia del
     * camino TiloPay de arriba, cuyo shape/ruta esta desalineado con la API
     * real de Payments - eso queda sin tocar, ver docs de la fase de pagos).
     */
    private function createCashIntent(Order $order): PaymentIntentResult
    {
        try {
            $response = Http::withHeaders([
                'X-Service-Token' => config('services.payments.service_token'),
                'X-Service-Audience' => 'ticketing',
                'X-Service-Scopes' => 'payments.write',
                'Idempotency-Key' => 'ticketing-order-'.$order->public_id,
            ])
                ->withoutVerifying() // vhost interno con certificado autofirmado (veraguas.internal)
                ->timeout(5)
                ->post(rtrim(config('services.payments.base_url'), '/').'/internal/v1/payment-intents', [
                    'source' => 'ticketing',
                    'external_reference' => $order->public_id,
                    // Payments guarda amount como enteros (unidad menor, centavos);
                    // Order.total en Ticketing es decimal:2, hay que convertir aqui.
                    'amount' => (int) round(((float) $order->total) * 100),
                    'currency' => $order->currency,
                    'provider' => 'cash',
                ]);

            if (! $response->successful()) {
                Log::warning('payments.create_cash_intent_failed', [
                    'order' => $order->public_id,
                    'status' => $response->status(),
                ]);

                return new PaymentIntentResult(success: false, errorMessage: 'Payments respondio '.$response->status());
            }

            $data = $response->json();

            return new PaymentIntentResult(success: true, intentId: $data['id'] ?? null, redirectUrl: null);
        } catch (\Throwable $e) {
            Log::error('payments.create_cash_intent_exception', [
                'order' => $order->public_id,
                'error' => $e->getMessage(),
            ]);

            return new PaymentIntentResult(success: false, errorMessage: 'No se pudo contactar a Payments.');
        }
    }

    public function refund(Order $order, ?string $reason = null): RefundResult
    {
        try {
            $response = Http::withToken(config('services.payments.internal_secret'))
                ->timeout(5)
                ->post(config('services.payments.base_url').'/refunds', [
                    'idempotency_key' => 'ticketing-refund-'.$order->public_id,
                    'payment_intent_id' => $order->payment_intent_id,
                    'reason' => $reason,
                ]);

            if (! $response->successful()) {
                return new RefundResult(success: false, errorMessage: 'Payments respondio '.$response->status());
            }

            $data = $response->json();

            return new RefundResult(success: true, refundId: $data['refund_id'] ?? null);
        } catch (\Throwable $e) {
            Log::error('payments.refund_exception', ['order' => $order->public_id, 'error' => $e->getMessage()]);

            return new RefundResult(success: false, errorMessage: 'No se pudo contactar a Payments.');
        }
    }
}
