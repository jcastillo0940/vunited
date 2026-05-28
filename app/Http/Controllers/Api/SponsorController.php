<?php

namespace App\Http\Controllers\Api;

use App\Domain\Sponsors\Models\Sponsor;
use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SponsorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Sponsor::query()
            ->where('is_active', true)
            ->orderByRaw("CASE tier WHEN 'main_partner' THEN 1 WHEN 'official_sponsor' THEN 2 WHEN 'strategic_ally' THEN 3 ELSE 4 END")
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($tier = $request->string('tier')->toString()) {
            $query->where('tier', $tier);
        }

        return SponsorResource::collection($query->get());
    }
}
