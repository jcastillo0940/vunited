<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_trips', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->foreignId('match_event_id')->nullable()->constrained('match_events')->nullOnDelete();
            $table->string('departure_location');
            $table->timestamp('departure_time');
            $table->timestamp('return_time')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->unsignedSmallInteger('capacity')->default(0);
            $table->unsignedSmallInteger('available_seats')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('departure_time');
            $table->index('match_event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_trips');
    }
};
