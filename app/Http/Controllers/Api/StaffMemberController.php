<?php

namespace App\Http\Controllers\Api;

use App\Domain\Squad\Models\StaffMember;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StaffMemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = StaffMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        return StaffMemberResource::collection($query->get());
    }
}
