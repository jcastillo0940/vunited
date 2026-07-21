<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class ServiceAuth { public function handle(Request $r,Closure $next,string $scope='payments.write'): Response { $token=(string)$r->header('X-Service-Token'); $aud=(string)$r->header('X-Service-Audience'); $scopes=array_filter(explode(' ',(string)$r->header('X-Service-Scopes'))); abort_unless($token!==''&&hash_equals((string)config('payments.service_token'),$token)&&in_array($aud,['store','ticketing','payments'],true)&&in_array($scope,$scopes,true),401,'Servicio no autorizado.'); return $next($r); } }
