<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class PaymentsPhase5Test extends TestCase { use RefreshDatabase;
 private function headers(array $scopes=['payments.write']):array { config(['payments.service_token'=>'test-secret']); return ['X-Service-Token'=>'test-secret','X-Service-Audience'=>'store','X-Service-Scopes'=>implode(' ',$scopes),'Idempotency-Key'=>'key-1','X-Correlation-ID'=>'corr-123456']; }
 public function test_internal_api_requires_service_auth():void{$this->postJson('/api/internal/v1/payment-intents',['source'=>'store','external_reference'=>'o1','amount'=>1000,'currency'=>'CRC'])->assertUnauthorized();}
 public function test_payment_intent_is_idempotent():void{$data=['source'=>'store','external_reference'=>'o1','amount'=>1000,'currency'=>'CRC'];$a=$this->withHeaders($this->headers())->postJson('/api/internal/v1/payment-intents',$data)->assertCreated();$b=$this->withHeaders($this->headers())->postJson('/api/internal/v1/payment-intents',$data)->assertOk();$this->assertSame($a->json('id'),$b->json('id'));}
 public function test_webhook_without_secret_never_verifies():void{$config=config('payments.tilopay.secret');config(['payments.tilopay.secret'=>null]);$this->postJson('/api/webhooks/v1/tilopay',['id'=>'evt-1'])->assertBadRequest();config(['payments.tilopay.secret'=>$config]);}
 public function test_webhook_duplicate_is_safe():void{config(['payments.tilopay.secret'=>'secret']);$payload=json_encode(['id'=>'evt-2']);$sig=hash_hmac('sha256',$payload,'secret');$this->call('POST','/api/webhooks/v1/tilopay',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_X_TILOPAY_SIGNATURE'=>$sig],$payload)->assertStatus(202);$this->call('POST','/api/webhooks/v1/tilopay',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_X_TILOPAY_SIGNATURE'=>$sig],$payload)->assertOk();}
}
