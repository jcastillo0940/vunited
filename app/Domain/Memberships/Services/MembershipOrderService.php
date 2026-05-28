<?php

namespace App\Domain\Memberships\Services;

use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Memberships\Models\MembershipPlan;
use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Providers\PayPalPaymentProvider;
use App\Domain\Payments\Services\PaymentLifecycleService;

class MembershipOrderService
{
    public function __construct(
        private readonly PaymentLifecycleService $lifecycle,
        private readonly PayPalPaymentProvider $provider,
    ) {}

    public function createOrder(array $data): array
    {
        $plan = MembershipPlan::activeForCode($data['membership_plan']);

        if ($plan === null) {
            throw new PaymentProviderException('No hay un plan de membresia activo disponible.');
        }

        $orderNumber = $this->generateOrderNumber();
        $planSnapshot = [
            'code' => $plan->code,
            'name' => $plan->name,
            'headline' => $plan->headline,
            'description' => $plan->description,
            'price' => $plan->price,
            'currency' => $plan->currency,
            'duration_months' => $plan->duration_months,
            'benefits' => $plan->benefits ?? [],
            'kit_items' => $plan->kit_items ?? [],
            'partner_discounts' => $plan->partner_discounts ?? [],
        ];

        $order = MembershipOrder::create([
            'order_number' => $orderNumber,
            'status' => MembershipOrderStatus::PendingPayment,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'identification_number' => $data['identification_number'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'age' => $data['age'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'membership_plan' => $plan->code,
            'membership_price' => $plan->price,
            'currency' => $plan->currency,
            'metadata' => [
                'plan_snapshot' => $planSnapshot,
            ],
        ]);

        $returnUrl = url('/registro-confirmado') . '?order=' . $orderNumber;

        $payment = $this->lifecycle->createPendingPayment([
            'payable_type' => MembershipOrder::class,
            'payable_id' => $order->id,
            'provider' => 'paypal',
            'currency' => $plan->currency,
            'amount' => $plan->price,
            'description' => "Membresia La Tribu - {$orderNumber}",
            'customer_email' => $data['email'],
            'customer_name' => $data['full_name'],
            'metadata' => [
                'paypal_return_url' => $returnUrl,
                'paypal_cancel_url' => url('/registro-tribu') . '?cancelled=1',
                'membership_plan' => $plan->code,
            ],
        ]);

        $result = $this->provider->createOrder($payment);

        if (! $result->success) {
            $this->lifecycle->markFailed($payment, $result->message ?? 'PayPal order creation failed.');
            $order->update(['status' => MembershipOrderStatus::Failed]);

            throw new PaymentProviderException(
                $result->message ?? 'No se pudo crear la orden en PayPal.',
            );
        }

        $this->lifecycle->markProviderCreated($payment, $result);

        return [
            'order' => $order->refresh(),
            'payment' => $payment->refresh(),
            'approve_url' => $result->redirectUrl,
        ];
    }

    private function generateOrderNumber(): string
    {
        $year = now()->year;
        $count = MembershipOrder::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('TRIBU-%d-%04d', $year, $count);
    }
}
