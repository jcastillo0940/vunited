<?php
namespace App\Domain\Forms\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable; use Illuminate\Database\Eloquent\Model;
#[Fillable(['public_id','form_type','name','email','phone','message','payload','consent_at','status','ip_address','user_agent'])]
class FormSubmission extends Model { protected function casts(): array { return ['payload'=>'array','consent_at'=>'datetime']; } }
