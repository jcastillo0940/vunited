<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('provider')->default('paypal')->unique();
            $table->string('mode', 10)->default('sandbox');
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->string('webhook_id')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_enabled')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
