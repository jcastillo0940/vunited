<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_order_id')->constrained('ticket_orders')->cascadeOnDelete();
            $table->foreignId('ticket_zone_id')->nullable()->constrained('ticket_zones')->nullOnDelete();
            $table->string('zone_name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('line_total', 10, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('ticket_order_id');
            $table->index('ticket_zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_order_items');
    }
};
