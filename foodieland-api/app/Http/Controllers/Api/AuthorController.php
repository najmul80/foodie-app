<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\RecipeResource;
use App\Http\Resources\UserResource;
use App\Models\User;

class AuthorController extends Controller
{
    /**
     * Display the specified author's profile.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Display a paginated list of recipes by the specified author.
     */
    public function getRecipes(User $user)
    {
        $recipes = $user->recipes()
            ->with(['categories', 'tags'])
            ->latest()
            ->paginate(12);

        return RecipeResource::collection($recipes);
    }

    /**
     * Display a paginated list of blog posts by the specified author.
     */
    public function getBlogs(User $user)
    {
        $blogs = $user->blogPosts()
            ->with(['user','categories', 'tags'])
            ->latest()
            ->paginate(12);

        return BlogPostResource::collection($blogs);
    }

    
}
