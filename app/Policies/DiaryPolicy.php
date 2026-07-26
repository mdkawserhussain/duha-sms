<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiaryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, \App\Models\DiaryEntry $diary): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $diary->teacher_id === $user->id;
        }

        if ($user->isGuardian()) {
            return $diary->class->students()->where('guardian_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, \App\Models\DiaryEntry $diary): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $diary->teacher_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, \App\Models\DiaryEntry $diary): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $diary->teacher_id === $user->id;
        }

        return false;
    }

    public function comment(User $user, \App\Models\DiaryEntry $diary): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuardian()) {
            return $diary->class->students()->where('guardian_id', $user->id)->exists();
        }

        return false;
    }
}
