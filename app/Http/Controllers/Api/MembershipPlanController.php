<?php

namespace App\Http\Controllers\Api;

use App\Domain\Memberships\Models\MembershipPlan;
use App\Http\Controllers\Controller;
use App\Http\Resources\MembershipPlanResource;
use Illuminate\Http\JsonResponse;

class MembershipPlanController extends Controller
{
    public function active(): JsonResponse
    {
        $plan = MembershipPlan::activeForCode('tribu');

        if ($plan === null) {
            return response()->json([
                'error' => 'No hay un plan de membresia activo disponible.',
            ], 404);
        }

        return (new MembershipPlanResource($plan))->response();
    }
}
