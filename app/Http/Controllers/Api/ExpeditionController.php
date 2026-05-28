<?php

namespace App\Http\Controllers\Api;

use App\Domain\Expedition\Models\BusTrip;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusTripResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpeditionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $trips = BusTrip::query()
            ->where('is_active', true)
            ->where('departure_time', '>', now())
            ->with('matchEvent')
            ->orderBy('departure_time')
            ->get();

        return BusTripResource::collection($trips);
    }
}
