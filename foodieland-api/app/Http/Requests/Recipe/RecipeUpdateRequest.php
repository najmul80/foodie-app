<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeUpdateRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'prep_time' => 'sometimes|required|integer|min:1',
            'cook_time' => 'sometimes|required|integer|min:1',
            'difficulty' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['Easy', 'Medium', 'Hard', 'easy', 'medium', 'hard']),
            ],
            'ingredients' => 'sometimes|required|array|min:1',
            'instructions' => 'sometimes|required|array|min:1',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
            'image' => 'sometimes|image|max:2048|mimes:jpg,jpeg,png,webp',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize difficulty to lowercase
        if ($this->has('difficulty')) {
            $this->merge([
                'difficulty' => strtolower($this->difficulty),
            ]);
        }

        // Ensure ingredients and instructions are arrays
        if ($this->has('ingredients') && is_string($this->ingredients)) {
            $this->merge([
                'ingredients' => array_filter(explode("\n", $this->ingredients), fn($i) => trim($i) !== ''),
            ]);
        }

        if ($this->has('instructions') && is_string($this->instructions)) {
            $this->merge([
                'instructions' => array_map(
                    fn($i) => ['description' => trim($i)],
                    array_filter(explode("\n", $this->instructions), fn($i) => trim($i) !== '')
                ),
            ]);
        }

        // Ensure category_ids is always an array
        if ($this->has('category_ids') && !is_array($this->category_ids)) {
            $this->merge([
                'category_ids' => [$this->category_ids],
            ]);
        }

        // Ensure tags is always an array
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }
    }
}
