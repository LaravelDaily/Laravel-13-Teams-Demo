<?php

namespace App\Policies;

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
        return $user->hasTeamPermission($user->currentTeam, 'post:create');
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->current_team_id !== $post->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, 'post:update');
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->current_team_id !== $post->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, 'post:delete');
    }
}
