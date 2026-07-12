<?php

namespace App\Policies;

use App\Models\ResourceRating;
use App\Models\User;

class ResourceRatingPolicy
{
    public function delete(User $user, ResourceRating $rating): bool
    {
        return $user->id === $rating->user_id || $user->isAdmin();
    }
}
