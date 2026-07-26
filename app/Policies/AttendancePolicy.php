<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, \App\Models\StudentAttendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $attendance->teacher_id === $user->id;
        }

        if ($user->isGuardian()) {
            return $attendance->student->guardian_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, \App\Models\StudentAttendance $attendance): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function delete(User $user, \App\Models\StudentAttendance $attendance): bool
    {
        return $user->isAdmin();
    }
}
