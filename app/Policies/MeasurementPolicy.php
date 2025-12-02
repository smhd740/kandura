<?php

namespace App\Policies;

use App\Models\Measurement;
use App\Models\User;

class MeasurementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Measurement $measurement): bool
    {
        return false;
    }

    public function delete(User $user, Measurement $measurement): bool
    {
        return false;
    }
}
