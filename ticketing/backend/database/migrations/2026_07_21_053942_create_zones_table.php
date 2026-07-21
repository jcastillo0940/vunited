<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            // general: cupo contado (capacity_*). seated: asientos individuales (tabla seats).
            $table->enum('kind', ['general', 'seated'])->default('general');
            $table->decimal('price', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->unsignedInteger('capacity_total')->default(0);
            $table->unsignedInteger('capacity_available')->default(0);
            $table->unsignedInteger('capacity_held')->default(0);
            $table->unsignedInteger('purchase_limit_per_buyer')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });

        // Defensa en profundidad: aunque la app siempre use UPDATE ... WHERE
        // atomico, la base nunca debe permitir capacidad negativa ni que la
        // suma de disponible+reservado exceda el total.
        DB::statement(
            'ALTER TABLE zones ADD CONSTRAINT zones_capacity_bounds_chk '.
            'CHECK (capacity_available + capacity_held <= capacity_total)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
