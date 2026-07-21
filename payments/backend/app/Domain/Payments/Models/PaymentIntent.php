<?php
namespace App\Domain\Payments\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Attributes\Fillable;
#[Fillable(['public_id','source','external_reference','amount','currency','status','provider','provider_reference','correlation_id','metadata'])] class PaymentIntent extends Model { protected function casts():array{return ['metadata'=>'array'];} }
