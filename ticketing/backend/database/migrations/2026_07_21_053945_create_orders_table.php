<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // public_id es lo unico que el cliente/frontend/QR conocen. El
            // id autoincremental nunca se expone (evita enumeracion).
            $table->ulid('public_id')->unique();
            // Numero humano derivado del id autoincremental (nunca de un
            // COUNT() bajo concurrencia, que puede colisionar) - se rellena
            // despues del insert, ver Order::booted().
            $table->string('order_number')->nullable()->unique();
            $table->foreignId('event_id')->constrained()->restrictOnDelete();
            $table->enum('status', [
                'draft', 'hold_active', 'pending_payment', 'payment_processing',
                'paid', 'tickets_issued', 'expired', 'cancelled',
                'refund_pending', 'refunded', 'failed',
            ])->default('draft');
            $table->string('customer_email');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            // Idempotencia del lado del cliente: reintentar el mismo checkout
            // no debe crear dos ordenes ni reclamar cupo dos veces.
            $table->string('idempotency_key')->nullable()->unique();
            $table->dateTime('hold_expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'hold_expires_at']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
