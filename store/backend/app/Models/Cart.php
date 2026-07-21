<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Attributes\Fillable; #[Fillable(['token','currency','expires_at'])] class Cart extends Model {protected function casts():array{return ['expires_at'=>'datetime'];} public function items(){return $this->hasMany(CartItem::class);}}
