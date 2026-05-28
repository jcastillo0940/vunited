<?php

namespace Tests\Feature\Admin\Payments;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_payments(): void
    {
        $admin = $this->createAdminWithPermissions(['payments.view']);
        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-MONITOR-001',
            'amount' => '120.00',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payments')
            ->assertOk()
            ->assertSee('Payments')
            ->assertSee((string) $payment->id)
            ->assertSee('PAYID-MONITOR-001');
    }

    public function test_admin_without_permission_cannot_view_payments(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/payments')
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_view_payment_detail(): void
    {
        $admin = $this->createAdminWithPermissions(['payments.view']);
        $order = MembershipOrder::factory()->create([
            'order_number' => 'TRIBU-2026-1010',
            'full_name' => 'Carlos Rios',
        ]);

        $payment = Payment::factory()->captured()->create([
            'payable_type' => MembershipOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-SHOW-001',
            'provider_capture_id' => 'CAP-SHOW-001',
            'metadata' => [
                'safe_note' => 'visible',
                'client_secret' => 'super-secret',
                'card_number' => '4111111111111111',
                'card_cvv' => '123',
            ],
            'provider_payload' => [
                'id' => 'PAYID-SHOW-001',
                'status' => 'COMPLETED',
                'client_secret' => 'super-secret',
                'card_number' => '4111111111111111',
                'card_cvv' => '123',
            ],
        ]);

        PaymentEvent::factory()->processed()->create([
            'payment_id' => $payment->id,
            'provider_event_id' => 'WH-SHOW-001',
            'provider_order_id' => 'PAYID-SHOW-001',
            'provider_capture_id' => 'CAP-SHOW-001',
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/payments/{$payment->id}")
            ->assertOk()
            ->assertSee('PAYID-SHOW-001')
            ->assertSee('CAP-SHOW-001')
            ->assertSee('visible')
            ->assertDontSee('super-secret')
            ->assertDontSee('4111111111111111')
            ->assertDontSee('123');
    }

    public function test_admin_with_permission_can_view_payment_events(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_events.view']);
        $event = PaymentEvent::factory()->create([
            'provider_event_id' => 'WH-MONITOR-001',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-events')
            ->assertOk()
            ->assertSee('Payment Events')
            ->assertSee($event->provider_event_id)
            ->assertSee('PAYMENT.CAPTURE.COMPLETED');
    }

    public function test_admin_without_permission_cannot_view_payment_events(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-events')
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_view_payment_event_detail(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_events.view']);
        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-EVENT-001',
        ]);
        $event = PaymentEvent::factory()->processed()->create([
            'payment_id' => $payment->id,
            'provider_event_id' => 'WH-DETAIL-001',
            'provider_order_id' => 'PAYID-EVENT-001',
            'headers' => [
                'paypal-auth-algo' => 'SHA256withRSA',
                'x-secret' => 'super-secret',
            ],
            'payload' => [
                'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
                'card_number' => '4111111111111111',
                'card_cvv' => '123',
            ],
            'error_message' => 'Sample processor note',
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/payment-events/{$event->id}")
            ->assertOk()
            ->assertSee('WH-DETAIL-001')
            ->assertSee('PAYID-EVENT-001')
            ->assertSee('Sample processor note')
            ->assertDontSee('super-secret')
            ->assertDontSee('4111111111111111')
            ->assertDontSee('123');
    }

    public function test_payments_and_events_filters_work_basic(): void
    {
        $admin = $this->createAdminWithPermissions(['payments.view', 'payment_events.view']);

        Payment::factory()->create([
            'provider_order_id' => 'PAYID-PENDING-001',
            'status' => PaymentStatus::Pending,
            'customer_email' => 'pending@example.com',
        ]);

        Payment::factory()->captured()->create([
            'provider_order_id' => 'PAYID-CAPTURED-001',
            'status' => PaymentStatus::Captured,
            'customer_email' => 'captured@example.com',
        ]);

        PaymentEvent::factory()->create([
            'provider_event_id' => 'WH-RECEIVED-001',
            'verification_status' => PaymentEventVerificationStatus::Verified,
            'processing_status' => PaymentEventProcessingStatus::Received,
            'event_type' => 'CHECKOUT.ORDER.APPROVED',
        ]);

        PaymentEvent::factory()->processed()->create([
            'provider_event_id' => 'WH-PROCESSED-001',
            'verification_status' => PaymentEventVerificationStatus::Verified,
            'processing_status' => PaymentEventProcessingStatus::Processed,
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payments?status=captured&search=captured@example.com')
            ->assertOk()
            ->assertSee('PAYID-CAPTURED-001')
            ->assertDontSee('PAYID-PENDING-001');

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-events?processing_status=processed&search=CAPTURE.COMPLETED')
            ->assertOk()
            ->assertSee('WH-PROCESSED-001')
            ->assertDontSee('WH-RECEIVED-001');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'payment-monitor-role-' . fake()->unique()->slug(),
            'label' => 'Payment Monitor Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
