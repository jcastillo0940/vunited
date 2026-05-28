<?php

namespace Database\Seeders;

use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        PaymentSetting::query()->firstOrCreate(
            ['provider' => 'paypal'],
            [
                'mode'          => 'sandbox',
                'client_id'     => null,
                'client_secret' => null,
                'webhook_id'    => null,
                'currency'      => 'USD',
                'is_enabled'    => false,
                'metadata'      => null,
            ],
        );
    }
}
