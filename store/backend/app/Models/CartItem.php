<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Attributes\Fillable; #[Fillable(['cart_id','product_id','quantity','unit_price'])] class CartItem extends Model {public $timestamps=false; public function product(){return $this->belongsTo(Product::class);}}
