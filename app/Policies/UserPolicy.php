<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the given profile.
     *
     * @param  \App\User  $userProfile
     * @param  \App\User  $signedInUser
     * @return mixed
     */
    public function update(User $userProfile, User $signedInUser)
    {
        return $userProfile->id == $signedInUser->id;
    }
}
