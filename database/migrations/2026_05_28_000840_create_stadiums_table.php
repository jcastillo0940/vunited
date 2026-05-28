<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stadiums', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('capacity')->nullable();
            $table->string('venue_type')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->string('map_embed_url')->nullable();
            $table->json('zones')->nullable();
            $table->json('matchday')->nullable();
            $table->json('rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stadiums');
    }
};
