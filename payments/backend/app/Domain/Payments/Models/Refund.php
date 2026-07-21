<?php
namespace App\Domain\Payments\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Attributes\Fillable;
#[Fillable(['public_id','payment_intent_id','amount','currency','status','provider_reference'])] class Refund extends Model { }
