<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view classes
    }

    public function view(User $user, \App\Models\ClassModel $class): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, \App\Models\ClassModel $class): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, \App\Models\ClassModel $class): bool
    {
        return $user->isAdmin();
    }

    public function manageRoutine(User $user, \App\Models\ClassModel $class): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $class->teacher_id === $user->id;
        }

        return false;
    }
}
