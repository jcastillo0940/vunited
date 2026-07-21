<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('number', 10)->nullable();
            $table->string('position')->nullable();
            $table->string('position_key', 30)->nullable();
            $table->string('category', 30)->default('first-team');
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('height', 20)->nullable();
            $table->string('weight', 20)->nullable();
            $table->string('dominant_foot', 20)->nullable();
            $table->text('photo_path')->nullable();
            $table->json('gallery')->nullable();
            $table->json('stats')->nullable();
            $table->json('attributes')->nullable();
            $table->text('biography')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
            $table->index(['category', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
