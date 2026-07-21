<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            // available -> held -> sold. blocked = fuera de venta (dano, staff, etc).
            $table->enum('status', ['available', 'held', 'sold', 'blocked'])->default('available');
            $table->timestamps();

            // Restriccion unica: una sola fila por asiento dentro de su zona.
            $table->unique(['zone_id', 'label']);
            $table->index(['zone_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
