<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_order_id')->constrained('ticket_orders')->cascadeOnDelete();
            $table->foreignId('ticket_order_item_id')->nullable()->constrained('ticket_order_items')->nullOnDelete();
            $table->char('token', 40)->unique();
            $table->text('qr_payload');
            $table->string('zone_name');
            $table->string('seat_label')->nullable();
            $table->string('status', 20)->default('issued');
            $table->timestamp('issued_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('ticket_order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_tickets');
    }
};
