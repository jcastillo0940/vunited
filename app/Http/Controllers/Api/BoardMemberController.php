<?php

namespace App\Http\Controllers\Api;

use App\Domain\Board\Models\BoardMember;
use App\Http\Controllers\Controller;
use App\Http\Resources\BoardMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BoardMemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = BoardMember::query()
            ->where('is_active', true)
            ->orderByRaw("CASE `group` WHEN 'presidency' THEN 1 WHEN 'executive_staff' THEN 2 WHEN 'board' THEN 3 WHEN 'transparency' THEN 4 ELSE 5 END")
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($group = $request->string('group')->toString()) {
            $query->where('group', $group);
        }

        return BoardMemberResource::collection($query->get());
    }
}
