<?php

namespace App\Http\Controllers\Api;

use App\Domain\Sports\Models\Club;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClubController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ClubResource::collection(
            Club::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        );
    }
}
