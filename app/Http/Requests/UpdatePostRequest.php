<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('team_id', $this->user()->current_team_id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'post_text' => ['required', 'string'],
        ];
    }
}
