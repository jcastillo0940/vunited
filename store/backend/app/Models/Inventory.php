<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Attributes\Fillable; #[Fillable(['product_id','available','reserved'])] class Inventory extends Model {protected $table='inventory'; public function availableToSell():int{return max(0,$this->available-$this->reserved);}}
