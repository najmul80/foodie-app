<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'prep_time' => 'required|integer|min:1',
            'cook_time' => 'required|integer|min:1',
            'difficulty' => [
                'required',
                'string',
                Rule::in(['Easy', 'Medium', 'Hard', 'easy', 'medium', 'hard']),
            ],
            'ingredients' => 'required|array|min:1',
            'instructions' => 'required|array|min:1',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'image_path' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('difficulty')) {
            $this->merge([
                'difficulty' => strtolower($this->difficulty),
            ]);
        }
    }
}
