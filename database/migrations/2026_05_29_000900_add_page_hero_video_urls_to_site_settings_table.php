<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('fanfest_hero_video_url')->nullable()->after('hero_video_url');
            $table->string('expedition_hero_video_url')->nullable()->after('fanfest_hero_video_url');
            $table->string('sponsors_hero_video_url')->nullable()->after('expedition_hero_video_url');
            $table->string('stadium_hero_video_url')->nullable()->after('sponsors_hero_video_url');
            $table->string('academy_hero_video_url')->nullable()->after('stadium_hero_video_url');
            $table->string('squad_hero_video_url')->nullable()->after('academy_hero_video_url');
            $table->string('news_hero_video_url')->nullable()->after('squad_hero_video_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'fanfest_hero_video_url',
                'expedition_hero_video_url',
                'sponsors_hero_video_url',
                'stadium_hero_video_url',
                'academy_hero_video_url',
                'squad_hero_video_url',
                'news_hero_video_url',
            ]);
        });
    }
};
