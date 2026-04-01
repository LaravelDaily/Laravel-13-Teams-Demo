<?php

namespace App\Traits;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function ($model) {
            $model->team_id ??= auth()->user()?->current_team_id;
        });
    }
}
