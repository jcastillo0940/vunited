<?php

namespace App\Http\Controllers\Api;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\FanFestEventResource;
use Illuminate\Http\JsonResponse;

class FanFestController extends Controller
{
    public function index(): JsonResponse|FanFestEventResource
    {
        $event = FanFestEvent::query()
            ->where('is_active', true)
            ->with('zones')
            ->orderByDesc('event_date')
            ->first();

        if ($event === null) {
            return response()->json(['data' => null], 200);
        }

        return new FanFestEventResource($event);
    }
}
