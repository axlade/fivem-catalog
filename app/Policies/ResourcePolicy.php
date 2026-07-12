<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    /**
     * Any authenticated user can submit a resource. Trusted roles (creator,
     * admin) get it published immediately; everyone else goes to moderation
     * — that distinction is applied in the controller, not here.
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Resource $resource): bool
    {
        return $user->id === $resource->user_id || $user->isAdmin();
    }

    public function delete(User $user, Resource $resource): bool
    {
        return $user->id === $resource->user_id || $user->isAdmin();
    }
}
