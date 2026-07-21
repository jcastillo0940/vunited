<?php
namespace App\Support\Audit;
use App\Domain\Audit\Models\AuditLog;
final class Audit { public static function write(string $module,string $action,?object $model=null,?array $old=null,?array $new=null): void { $r=app()->bound('request')?request():null; AuditLog::create(['admin_user_id'=>$r?->user()?->id,'module'=>$module,'action'=>$action,'auditable_type'=>$model?get_class($model):null,'auditable_id'=>$model?->getKey(),'old_values'=>$old,'new_values'=>$new,'ip_address'=>$r?->ip(),'user_agent'=>$r?->userAgent()]); } }
