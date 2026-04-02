<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return $user->current_team_id === $post->team_id;
    }

    public function create(User $user): bool
    {
        $role = $user->teamRole($user->currentTeam);

        return $role !== null && $role->isAtLeast(TeamRole::Member);
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->current_team_id !== $post->team_id) {
            return false;
        }

        $role = $user->teamRole($user->currentTeam);

        return $role !== null && $role->isAtLeast(TeamRole::Member);
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->current_team_id !== $post->team_id) {
            return false;
        }

        $role = $user->teamRole($user->currentTeam);

        return $role !== null && $role->isAtLeast(TeamRole::Admin);
    }
}
