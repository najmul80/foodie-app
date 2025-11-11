<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image_path,
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'difficulty' => $this->difficulty,
            'author' => new UserResource($this->whenLoaded('user')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'ingredients' => $this->ingredients,
            'instructions' => $this->instructions,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'nutrition_facts' => $this->nutrition_facts,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->toFormattedDateString(),
        ];
    }
}
