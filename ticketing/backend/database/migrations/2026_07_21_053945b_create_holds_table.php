<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holds', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('seat_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->enum('status', ['active', 'consumed', 'released', 'expired'])->default('active');
            $table->dateTime('expires_at');
            $table->dateTime('released_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holds');
    }
};
