<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, \App\Models\Evaluation $evaluation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $evaluation->teacher_id === $user->id;
        }

        if ($user->isGuardian()) {
            return $evaluation->student->guardian_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, \App\Models\Evaluation $evaluation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $evaluation->teacher_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, \App\Models\Evaluation $evaluation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $evaluation->teacher_id === $user->id;
        }

        return false;
    }
}
