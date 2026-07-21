<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('league_standings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('competition')->default('LPF');
            $table->string('season');
            $table->unsignedSmallInteger('position')->default(0);
            $table->unsignedSmallInteger('played')->default(0);
            $table->unsignedSmallInteger('won')->default(0);
            $table->unsignedSmallInteger('drawn')->default(0);
            $table->unsignedSmallInteger('lost')->default(0);
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->smallInteger('goal_difference')->default(0);
            $table->unsignedSmallInteger('points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'competition', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_standings');
    }
};
