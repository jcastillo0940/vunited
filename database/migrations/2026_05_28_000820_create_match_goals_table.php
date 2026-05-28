<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_goals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('scorer_name')->nullable();
            $table->unsignedSmallInteger('minute')->nullable();
            $table->boolean('is_own_goal')->default(false);
            $table->boolean('is_penalty')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_goals');
    }
};
