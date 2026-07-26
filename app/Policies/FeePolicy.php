<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuardian();
    }

    public function viewStructure(User $user): bool
    {
        return $user->isAdmin();
    }

    public function createStructure(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateStructure(User $user): bool
    {
        return $user->isAdmin();
    }

    public function deleteStructure(User $user): bool
    {
        return $user->isAdmin();
    }

    public function recordPayment(User $user): bool
    {
        return $user->isAdmin();
    }

    public function viewRecords(User $user, \App\Models\FeeRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuardian()) {
            return $record->student->guardian_id === $user->id;
        }

        return false;
    }
}
