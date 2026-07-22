<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void { Schema::create('admins',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->unique();$t->string('password');$t->boolean('is_active')->default(true);$t->timestamp('last_login_at')->nullable();$t->rememberToken();$t->timestamps();}); }
 public function down():void { Schema::dropIfExists('admins'); }
};
