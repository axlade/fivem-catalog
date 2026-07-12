<?php

namespace App\Policies;

use App\Models\ResourceComment;
use App\Models\User;

class ResourceCommentPolicy
{
    public function delete(User $user, ResourceComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }
}
