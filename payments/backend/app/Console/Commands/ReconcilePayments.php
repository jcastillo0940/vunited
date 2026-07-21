<?php
namespace App\Console\Commands;
use Illuminate\Console\Command; use Illuminate\Support\Facades\DB;
class ReconcilePayments extends Command { protected $signature='payments:reconcile'; protected $description='Genera un corte de conciliación local para revisión'; public function handle():int{$count=DB::table('payment_intents')->count();DB::table('reconciliations')->insert(['event_type'=>'local.snapshot','status'=>'pending','payload'=>json_encode(['payment_intents'=>$count,'generated_at'=>now()->toIso8601String()]),'created_at'=>now(),'updated_at'=>now()]);$this->info("Conciliación creada: {$count} intents");return self::SUCCESS;} }
