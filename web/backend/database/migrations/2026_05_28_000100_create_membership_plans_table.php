<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->unsignedInteger('duration_months')->default(12);
            $table->json('benefits')->nullable();
            $table->json('kit_items')->nullable();
            $table->json('partner_discounts')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
