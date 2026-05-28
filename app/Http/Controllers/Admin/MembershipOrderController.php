<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Memberships\Models\MembershipOrder;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MembershipOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = MembershipOrder::query()->with('payment');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('admin.membership-orders.index', [
            'orders' => $query->latest()->get(),
            'filters' => [
                'status' => $request->string('status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(MembershipOrder $membershipOrder): View
    {
        $membershipOrder->load('payment');

        return view('admin.membership-orders.show', [
            'order' => $membershipOrder,
            'payment' => $membershipOrder->payment,
            'safeMetadata' => $this->sanitize($membershipOrder->metadata),
        ]);
    }

    private function sanitize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $sanitized = [];

        foreach ($value as $key => $item) {
            $normalizedKey = is_string($key) ? strtolower($key) : (string) $key;

            if (str_contains($normalizedKey, 'secret')
                || str_contains($normalizedKey, 'card')
                || str_contains($normalizedKey, 'cvv')
                || str_contains($normalizedKey, 'authorization')) {
                $sanitized[$key] = '***';
                continue;
            }

            $sanitized[$key] = is_array($item) ? $this->sanitize($item) : $item;
        }

        return $sanitized;
    }
}
