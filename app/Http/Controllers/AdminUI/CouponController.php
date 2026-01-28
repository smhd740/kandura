<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\User;
use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;

class CouponController extends Controller
{
    public function index()
    {
        $query = Coupon::with('allowedUsers')->latest();

        if (request()->filled('search')) {
            $query->search(request('search'));
        }

        if (request()->filled('type')) {
            $query->byType(request('type'));
        }

        if (request()->filled('status')) {
            $query->byStatus(request('status'));
        }

        $coupons = $query->paginate(15);

        $stats = [
            'total_coupons'   => Coupon::count(),
            'active_coupons'  => Coupon::active()->count(),
            'expired_coupons' => Coupon::where('expires_at', '<', now())->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $users = User::whereNotIn('role', ['admin', 'super_admin'])
            ->orderBy('name')
            ->get();

        return view('admin.coupons.create', compact('users'));
    }

    public function store(StoreCouponRequest $request)
{
    $data = $request->validated();

    // Handle checkbox - if not checked, set to false
    $data['is_active'] = $request->has('is_active') ? true : false;

    $coupon = Coupon::create($data);

    if ($coupon->is_user_specific && $request->filled('allowed_users')) {
        $coupon->allowedUsers()->attach($request->allowed_users);
    }

    return redirect()
        ->route('admin.coupons.index')
        ->with('success', __('Coupon created successfully.'));
}

    public function edit(Coupon $coupon)
    {
        $coupon->load('allowedUsers');

        $users = User::whereNotIn('role', ['admin', 'super_admin'])
            ->orderBy('name')
            ->get();

        return view('admin.coupons.edit', compact('coupon', 'users'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());

        if ($coupon->is_user_specific && $request->filled('allowed_users')) {
            $coupon->allowedUsers()->sync($request->allowed_users);
        } else {
            $coupon->allowedUsers()->detach();
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->usages()->exists()) {
            return back()->with('error', __('Cannot delete coupon that has been used.'));
        }

        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('Coupon deleted successfully.'));
    }

    public function usages(Coupon $coupon)
    {
        $coupon->load(['usages.user', 'usages.order']);

        return view('admin.coupons.usages', compact('coupon'));
    }

    public function toggleStatus(Coupon $coupon)
{
    $coupon->update([
        'is_active' => !$coupon->is_active
    ]);

    $status = $coupon->is_active ? 'activated' : 'deactivated';

    return redirect()
        ->back()
        ->with('success', __("Coupon {$status} successfully."));
}
}
