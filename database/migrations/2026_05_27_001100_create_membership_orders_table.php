<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('status', 30)->default('pending_payment');
            $table->string('full_name');
            $table->string('identification_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('membership_plan', 30)->default('tribu');
            $table->decimal('membership_price', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_orders');
    }
};
