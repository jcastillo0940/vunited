<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void { Schema::table('orders',function(Blueprint $t){$t->string('payment_method')->nullable()->after('payment_public_id');$t->string('cash_confirmed_by')->nullable();$t->dateTime('cash_confirmed_at')->nullable();}); }
 public function down():void { Schema::table('orders',function(Blueprint $t){$t->dropColumn(['payment_method','cash_confirmed_by','cash_confirmed_at']);}); }
};
