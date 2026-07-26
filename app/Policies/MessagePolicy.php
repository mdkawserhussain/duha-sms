<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, \App\Models\Message $message): bool
    {
        return $message->sender_id === $user->id || $message->recipient_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, \App\Models\Message $message): bool
    {
        return $message->sender_id === $user->id && !$message->is_read;
    }

    public function delete(User $user, \App\Models\Message $message): bool
    {
        return $message->sender_id === $user->id || $user->isAdmin();
    }
}
