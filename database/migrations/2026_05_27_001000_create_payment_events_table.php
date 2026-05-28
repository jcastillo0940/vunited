<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('provider', 50)->default('paypal');
            $table->string('provider_event_id')->nullable();
            $table->string('event_type');
            $table->string('resource_type')->nullable();
            $table->string('provider_order_id')->nullable();
            $table->string('provider_capture_id')->nullable();
            $table->string('verification_status', 30)->default('pending');
            $table->string('processing_status', 30)->default('received');
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('payment_id');
            $table->index('provider_order_id');
            $table->index('provider_capture_id');
            $table->index('event_type');
            $table->index('verification_status');
            $table->index('processing_status');
            $table->index(['provider', 'provider_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
