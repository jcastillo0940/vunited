<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Device::query()->with('operator:id,name')->get(),
        ]);
    }

    public function revoke(string $publicId): JsonResponse
    {
        $device = Device::query()->where('public_id', $publicId)->firstOrFail();
        $device->update(['is_active' => false, 'revoked_at' => now()]);

        return response()->json(['message' => 'Dispositivo revocado.']);
    }
}
