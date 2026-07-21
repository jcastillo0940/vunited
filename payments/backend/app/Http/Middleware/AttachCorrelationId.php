<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Illuminate\Support\Str;
class AttachCorrelationId { public function handle(Request $r,Closure $next){$id=$r->header('X-Correlation-ID'); if(!is_string($id)||!preg_match('/^[A-Za-z0-9._:-]{8,128}$/',$id))$id=(string)Str::uuid();$r->attributes->set('correlation_id',$id);$res=$next($r);$res->headers->set('X-Correlation-ID',$id);return $res;} }
