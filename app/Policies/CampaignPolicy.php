<?php

namespace App\Policies;

use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function view(User $user, EmailCampaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }

    public function update(User $user, EmailCampaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }

    public function delete(User $user, EmailCampaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }
} 