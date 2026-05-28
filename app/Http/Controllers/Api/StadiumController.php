<?php

namespace App\Http\Controllers\Api;

use App\Domain\Stadium\Models\Stadium;
use App\Http\Controllers\Controller;
use App\Http\Resources\StadiumResource;
use Illuminate\Http\JsonResponse;

class StadiumController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $stadium = Stadium::query()->where('is_active', true)->first();

        if ($stadium === null) {
            return response()->json(['error' => 'Estadio no disponible.'], 404);
        }

        return (new StadiumResource($stadium))->response();
    }
}
