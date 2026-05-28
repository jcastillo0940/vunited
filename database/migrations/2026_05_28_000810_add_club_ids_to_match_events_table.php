<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_events', function (Blueprint $table): void {
            $table->foreignId('home_club_id')->nullable()->after('away_team')
                ->constrained('clubs')->nullOnDelete();
            $table->foreignId('away_club_id')->nullable()->after('home_club_id')
                ->constrained('clubs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table): void {
            $table->dropForeignIdFor(\App\Domain\Sports\Models\Club::class, 'home_club_id');
            $table->dropForeignIdFor(\App\Domain\Sports\Models\Club::class, 'away_club_id');
            $table->dropColumn(['home_club_id', 'away_club_id']);
        });
    }
};
