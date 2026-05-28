<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Memberships\Models\MembershipPlan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Memberships\StoreMembershipPlanRequest;
use App\Http\Requests\Admin\Memberships\UpdateMembershipPlanRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipPlanController extends Controller
{
    public function index(): View
    {
        return view('admin.membership-plans.index', [
            'plans' => MembershipPlan::query()->orderBy('sort_order')->orderBy('code')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.membership-plans.create', [
            'plan' => new MembershipPlan([
                'currency' => 'USD',
                'duration_months' => 12,
                'sort_order' => 0,
                'is_active' => false,
            ]),
        ]);
    }

    public function store(StoreMembershipPlanRequest $request): RedirectResponse
    {
        $plan = MembershipPlan::query()->create($request->validated());
        $plan->deactivateSiblings();
        $plan->refresh();

        RecordsAdminAudit::created('membership_plans', $plan, $request);

        return redirect()->route('admin.membership-plans.index');
    }

    public function edit(MembershipPlan $membershipPlan): View
    {
        return view('admin.membership-plans.edit', [
            'plan' => $membershipPlan,
        ]);
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membershipPlan): RedirectResponse
    {
        $before = $membershipPlan->attributesToArray();
        $membershipPlan->update($request->validated());
        $membershipPlan->deactivateSiblings();
        $membershipPlan->refresh();

        RecordsAdminAudit::updated('membership_plans', $membershipPlan, $request, $before);

        return redirect()->route('admin.membership-plans.index');
    }

    public function destroy(Request $request, MembershipPlan $membershipPlan): RedirectResponse
    {
        if ($membershipPlan->membershipOrders()->exists()) {
            return redirect()
                ->route('admin.membership-plans.index')
                ->with('error', 'No puedes eliminar un plan que ya tiene ordenes asociadas.');
        }

        $before = $membershipPlan->attributesToArray();
        $membershipPlan->delete();

        RecordsAdminAudit::deleted('membership_plans', $membershipPlan, $request, $before);

        return redirect()->route('admin.membership-plans.index');
    }
}
