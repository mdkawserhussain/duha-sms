<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, \App\Models\Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->classes()->where('id', $student->class_id)->exists();
        }

        if ($user->isGuardian()) {
            return $student->guardian_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, \App\Models\Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuardian()) {
            return $student->guardian_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, \App\Models\Student $student): bool
    {
        return $user->isAdmin();
    }

    public function manageAttendance(User $user, \App\Models\Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->classes()->where('id', $student->class_id)->exists();
        }

        return false;
    }
}
