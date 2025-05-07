<?php

namespace App\Policies;

use App\Models\SubscriberList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriberListPolicy
{
    use HandlesAuthorization;

    public function view(User $user, SubscriberList $list)
    {
        return $user->id === $list->user_id;
    }

    public function update(User $user, SubscriberList $list)
    {
        return $user->id === $list->user_id;
    }

    public function delete(User $user, SubscriberList $list)
    {
        return $user->id === $list->user_id;
    }
} 