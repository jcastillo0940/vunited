<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            // null = todas las puertas del evento
            $table->foreignId('door_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['operator_id', 'event_id', 'door_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_assignments');
    }
};
