<?php

namespace App\Support\Audit;

use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordsAdminAudit
{
    public static function created(string $module, Model $auditable, Request $request, Model|array|null $newValues = null): AuditLog
    {
        return self::record(
            module: $module,
            action: 'created',
            auditable: $auditable,
            request: $request,
            oldValues: null,
            newValues: $newValues ?? $auditable,
        );
    }

    public static function updated(string $module, Model $auditable, Request $request, Model|array|null $oldValues, Model|array|null $newValues = null): AuditLog
    {
        return self::record(
            module: $module,
            action: 'updated',
            auditable: $auditable,
            request: $request,
            oldValues: $oldValues,
            newValues: $newValues ?? $auditable,
        );
    }

    public static function deleted(string $module, Model $auditable, Request $request, Model|array|null $oldValues = null): AuditLog
    {
        return self::record(
            module: $module,
            action: 'deleted',
            auditable: $auditable,
            request: $request,
            oldValues: $oldValues ?? $auditable,
            newValues: null,
        );
    }

    public static function record(
        string $module,
        string $action,
        Model $auditable,
        Request $request,
        Model|array|null $oldValues = null,
        Model|array|null $newValues = null,
    ): AuditLog {
        /** @var AdminUser|null $adminUser */
        $adminUser = Auth::guard('admin')->user();

        return AuditLog::query()->create([
            'admin_user_id' => $adminUser?->id,
            'module' => $module,
            'action' => $action,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'old_values' => AuditablePayload::from($oldValues),
            'new_values' => AuditablePayload::from($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
