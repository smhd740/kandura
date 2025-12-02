<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;

class DesignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Design $design): bool
    {
        // Admin can view any design
        if ($user->isAdmin()) {
            return true;
        }

        // User can view their own designs
        return $user->id === $design->user_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    public function update(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    public function delete(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    public function restore(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    public function forceDelete(User $user, Design $design): bool
    {
        return $user->isSuperAdmin();
    }

    public function viewAll(User $user): bool
    {
        return $user->isAdmin();
    }
}
