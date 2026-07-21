<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\Device;
use App\Domain\Ticketing\Services\TicketValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ValidationController
{
    public function __construct(private readonly TicketValidationService $validation) {}

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'door_id' => ['nullable', 'integer'],
            'device_public_id' => ['nullable', 'string'],
        ]);

        $operator = $request->user();
        $deviceId = null;

        if (! empty($data['device_public_id'])) {
            $device = Device::query()->where('public_id', $data['device_public_id'])->first();
            if ($device && $device->is_active && $device->revoked_at === null) {
                $device->update(['last_seen_at' => now(), 'operator_id' => $operator?->id]);
                $deviceId = $device->id;
            }
        }

        $correlationId = $request->attributes->get('correlation_id') ?? (string) Str::uuid();

        $result = $this->validation->validate(
            token: $data['token'],
            doorId: $data['door_id'] ?? null,
            operatorId: $operator?->id,
            deviceId: $deviceId,
            correlationId: $correlationId,
        );

        $status = $result['valid'] ? 200 : 409;

        return response()->json($result, $status);
    }
}
