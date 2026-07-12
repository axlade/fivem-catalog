<?php

namespace App\Policies;

use App\Models\ServiceReview;
use App\Models\User;

class ServiceReviewPolicy
{
    public function delete(User $user, ServiceReview $review): bool
    {
        return $user->id === $review->reviewer_id || $user->isAdmin();
    }
}
