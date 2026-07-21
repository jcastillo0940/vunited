<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fan_fest_events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->string('location')->nullable();
            $table->text('hero_image_path')->nullable();
            $table->json('schedule')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fan_fest_events');
    }
};
