<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class BelongsToCurrentTeam implements ValidationRule
{
    public function __construct(
        private string $table,
        private string $column = 'id',
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table($this->table)
            ->where($this->column, $value)
            ->where('team_id', auth()->user()->current_team_id)
            ->exists();

        if (! $exists) {
            $fail('The selected :attribute does not belong to your team.');
        }
    }
}
