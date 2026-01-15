<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     * Admin: can view all orders
     * User: can view only their own orders
     */
    public function viewAny(User $user): bool
    {
        return true; // Will be filtered in controller
    }

    /**
     * Determine whether the user can view the order.
     * Admin: can view any order
     * User: can view only their own order
     */
    public function view(User $user, Order $order): bool
    {
        return $user->hasRole('admin')
            || $user->hasRole('super_admin')
            || $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can create orders.
     * Any authenticated user can create orders
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the order.
     * Users cannot update orders (only cancel)
     * Admins cannot update via this method (only status via updateStatus)
     */
    public function update(User $user, Order $order): bool
    {
        return false; // Orders cannot be updated
    }

    /**
     * Determine whether the user can delete the order.
     * No one can delete orders (only cancel/soft delete)
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can cancel the order.
     * User: can cancel their own order if status = pending
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id
            && $order->status === 'pending';
    }

    /**
     * Determine whether the user can update order status.
     * Only Admin can update order status
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return ($user->hasRole('admin') || $user->hasRole('super_admin'))
            && $order->canUpdateStatus();
    }

    /**
     * Determine whether the user can view order statistics.
     * Only Admin can view statistics
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the order.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasRole('super_admin');
    }
}
