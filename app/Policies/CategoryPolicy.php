<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return $user->current_team_id === $category->team_id;
    }

    public function create(User $user): bool
    {
        return $user->hasTeamPermission($user->currentTeam, 'category:create');
    }

    public function update(User $user, Category $category): bool
    {
        if ($user->current_team_id !== $category->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, 'category:update');
    }

    public function delete(User $user, Category $category): bool
    {
        if ($user->current_team_id !== $category->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, 'category:delete');
    }
}
