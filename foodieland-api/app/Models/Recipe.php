<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'ingredients',
        'instructions',
        'image_path',
        'prep_time',
        'cook_time',
        'difficulty',
        'nutrition_facts',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'nutrition_facts' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_recipe');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorited_recipes');
    }
}
