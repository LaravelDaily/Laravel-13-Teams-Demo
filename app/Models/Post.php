<?php

namespace App\Models;

use App\Models\Scopes\TeamScope;
use App\Traits\BelongsToTeam;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'category_id', 'title', 'post_text'])]
#[ScopedBy([TeamScope::class])]
class Post extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
