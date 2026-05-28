<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->nullableMorphs('payable');
            $table->string('provider', 50)->default('paypal');
            $table->string('provider_order_id')->nullable();
            $table->string('provider_capture_id')->nullable();
            $table->string('status', 50)->default('pending');
            $table->char('currency', 3)->default('USD');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->json('metadata')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'provider_order_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
