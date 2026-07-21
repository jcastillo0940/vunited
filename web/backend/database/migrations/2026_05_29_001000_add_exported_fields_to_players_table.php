<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            $table->boolean('is_exported')->default(false)->after('is_active');
            $table->string('foreign_club')->nullable()->after('is_exported');
            $table->string('foreign_league')->nullable()->after('foreign_club');
            $table->string('foreign_country')->nullable()->after('foreign_league');
            $table->string('foreign_club_logo')->nullable()->after('foreign_country');
            $table->json('achievements')->nullable()->after('foreign_club_logo');

            $table->index('is_exported');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            $table->dropIndex(['is_exported']);
            $table->dropColumn(['is_exported', 'foreign_club', 'foreign_league', 'foreign_country', 'foreign_club_logo', 'achievements']);
        });
    }
};
