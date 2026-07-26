<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardianPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, \App\Models\User $guardian): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuardian()) {
            return $user->id === $guardian->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true; // Public registration
    }

    public function update(User $user, \App\Models\User $guardian): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuardian()) {
            return $user->id === $guardian->id;
        }

        return false;
    }

    public function delete(User $user, \App\Models\User $guardian): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, \App\Models\User $guardian): bool
    {
        return $user->isAdmin();
    }
}
