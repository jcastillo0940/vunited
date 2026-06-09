<?php

namespace App\Http\Controllers\Api;

use App\Domain\Squad\Models\Player;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlayerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Player::query()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        if ($positionKey = $request->string('position')->toString()) {
            $query->where('position_key', $positionKey);
        }

        if ($request->boolean('exported')) {
            $query->where('is_exported', true);
        }

        return PlayerResource::collection($query->get());
    }

    public function show(string $slug): JsonResponse
    {
        $player = Player::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($player === null) {
            return response()->json(['error' => 'Jugador no encontrado.'], 404);
        }

        return (new PlayerResource($player))->response();
    }
}
