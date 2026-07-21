<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('categories',function(Blueprint $t){$t->id();$t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();$t->string('name');$t->string('slug')->unique();$t->boolean('active')->default(true);$t->timestamps();});
  Schema::create('products',function(Blueprint $t){$t->id();$t->foreignId('category_id')->nullable()->constrained();$t->string('name');$t->string('slug')->unique();$t->text('description')->nullable();$t->string('sku')->unique();$t->unsignedBigInteger('price');$t->unsignedBigInteger('sale_price')->nullable();$t->timestamp('sale_starts_at')->nullable();$t->timestamp('sale_ends_at')->nullable();$t->string('status')->default('active');$t->boolean('featured')->default(false);$t->json('metadata')->nullable();$t->timestamps();});
  Schema::create('inventory',function(Blueprint $t){$t->id();$t->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();$t->unsignedInteger('available')->default(0);$t->unsignedInteger('reserved')->default(0);$t->timestamps();});
  Schema::create('carts',function(Blueprint $t){$t->id();$t->uuid('token')->unique();$t->string('currency',3)->default('CRC');$t->timestamp('expires_at');$t->timestamps();});
  Schema::create('cart_items',function(Blueprint $t){$t->id();$t->foreignId('cart_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained();$t->unsignedInteger('quantity');$t->unsignedBigInteger('unit_price');$t->unique(['cart_id','product_id']);});
  Schema::create('orders',function(Blueprint $t){$t->id();$t->uuid('public_id')->unique();$t->string('status')->default('draft');$t->string('email');$t->json('shipping')->nullable();$t->unsignedBigInteger('subtotal');$t->unsignedBigInteger('total');$t->string('currency',3);$t->string('payment_public_id')->nullable()->index();$t->string('correlation_id')->nullable();$t->timestamps();});
  Schema::create('order_items',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained();$t->unsignedInteger('quantity');$t->unsignedBigInteger('unit_price');});
  Schema::create('stock_movements',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained();$t->string('type');$t->integer('quantity');$t->string('reference')->nullable();$t->timestamps();});
 }
 public function down():void { foreach(['stock_movements','order_items','orders','cart_items','carts','inventory','products','categories'] as $t) Schema::dropIfExists($t); }
};
