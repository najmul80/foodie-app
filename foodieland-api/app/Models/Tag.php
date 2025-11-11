<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function recipes()
    {
        return $this->morphedByMany(Recipe::class, 'taggable');
    }

    public function blogPosts()
    {
        return $this->morphedByMany(BlogPost::class, 'taggable');
    }
}
