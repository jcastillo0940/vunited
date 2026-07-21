<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_users', function (Blueprint $table): void {
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('two_factor_enabled');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->timestamp('revoked_at')->nullable()->after('locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('admin_users', function (Blueprint $table): void {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_enabled',
                'failed_login_attempts',
                'locked_until',
                'revoked_at',
            ]);
        });
    }
};
