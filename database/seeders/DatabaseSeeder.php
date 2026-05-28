<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AccessControlSeeder::class,
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            StadiumSeeder::class,
            ClubSeeder::class,
            MatchEventSeeder::class,
            TicketZoneSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            PaymentSettingSeeder::class,
            MembershipPlanSeeder::class,
            PlayerSeeder::class,
            StaffMemberSeeder::class,
            SponsorSeeder::class,
            BoardMemberSeeder::class,
            FanFestSeeder::class,
            BusTripSeeder::class,
            LeagueStandingSeeder::class,
        ]);
    }
}
