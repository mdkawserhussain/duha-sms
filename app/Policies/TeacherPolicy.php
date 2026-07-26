<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, \App\Models\User $teacher): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->id === $teacher->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, \App\Models\User $teacher): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, \App\Models\User $teacher): bool
    {
        return $user->isAdmin();
    }

    public function assignClasses(User $user, \App\Models\User $teacher): bool
    {
        return $user->isAdmin();
    }
}
