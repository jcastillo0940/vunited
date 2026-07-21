<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('code')->unique();
            $table->string('home_team');
            $table->string('away_team');
            $table->string('competition')->nullable();
            $table->string('round_label')->nullable();
            $table->dateTime('starts_at');
            $table->string('venue_name')->nullable();
            $table->string('venue_location')->nullable();
            // scheduled: aun no abre venta. on_sale: venta activa.
            // sale_closed: se cerro la venta (manual o por sales_close_at).
            // in_progress/finished: informativo. cancelled/postponed: bloquea venta y validacion.
            $table->enum('status', [
                'scheduled', 'on_sale', 'sale_closed', 'in_progress', 'finished', 'cancelled', 'postponed',
            ])->default('scheduled');
            $table->dateTime('sales_open_at')->nullable();
            $table->dateTime('sales_close_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('purchase_limit_per_buyer')->default(6);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_visible']);
            $table->index('starts_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
