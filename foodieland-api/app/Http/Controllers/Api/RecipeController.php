<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\RecipeStoreRequest;
use App\Http\Requests\Recipe\RecipeUpdateRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 12);
        $search = $request->query('search');

        $query = Recipe::query()->with(['user', 'categories', 'tags']);

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $recipes = $query->latest()->paginate($limit);

        return RecipeResource::collection($recipes)
            ->additional([
                'status' => 'success',
                'message' => $recipes->isEmpty() ? 'No recipes found.' : 'Recipes retrieved successfully.',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecipeStoreRequest $request)
    {
        $this->authorize('create', Recipe::class);
        $data = $request->validated();

        try {
            // Image handling
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
                            .'-'.time().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('recipes', $filename, 'public');
            }

            // Unique slug generation
            $baseSlug = Str::slug($data['title']);
            $slug = $baseSlug;
            $i = 1;
            while (Recipe::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$i++;
            }

            $recipe = $request->user()->recipes()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'prep_time' => $data['prep_time'],
                'cook_time' => $data['cook_time'],
                'difficulty' => $data['difficulty'],
                'ingredients' => $data['ingredients'],
                'instructions' => $data['instructions'],
                'image_path' => $imagePath,
            ]);

            // Sync categories
            if (! empty($data['category_ids'])) {
                $recipe->categories()->sync($data['category_ids']);
            }

            // Tag manage
            if (! (empty($data['tags']))) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $trimmedTagName = trim($tagName);
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($trimmedTagName)],
                        ['name' => ucwords($trimmedTagName)],
                    );
                    $tagIds[] = $tag->id;
                }
                $recipe->tags()->sync($tagIds);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Recipe created successfully.',
                'data' => new RecipeResource($recipe->load(['categories', 'tags'])),
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            Log::error('Recipe creation failed', [
                'user_id' => $request->user()->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while creating the recipe. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['user', 'categories', 'tags', 'comments.user']);

        return response()->json([
            'status' => 'success',
            'data' => new RecipeResource($recipe),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeUpdateRequest $request, Recipe $recipe)
    {
        
        $this->authorize('update', $recipe);

        $data = $request->validated();

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                if ($recipe->image_path && Storage::disk('public')->exists($recipe->image_path)) {
                    Storage::disk('public')->delete($recipe->image_path);
                }
                $image = $request->file('image');
                $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
                            .'-'.time().'.'.$image->getClientOriginalExtension();
                $data['image_path'] = $image->storeAs('recipes', $filename, 'public');
            }

            // Update slug if title changed (with uniqueness)
            if (isset($data['title']) && $data['title'] !== $recipe->title) {
                $baseSlug = Str::slug($data['title']);
                $slug = $baseSlug;
                $i = 1;
                while (Recipe::where('slug', $slug)->where('id', '!=', $recipe->id)->exists()) {
                    $slug = $baseSlug.'-'.$i++;
                }
                $data['slug'] = $slug;
            }

            // Update recipe
            $recipe->update($data);

            // Sync categories if provided
            if (isset($data['category_ids'])) {
                $recipe->categories()->sync($data['category_ids']);
            }
            if (! empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug(trim($tagName))],
                        ['name' => ucwords(trim($tagName))]
                    );
                    $tagIds[] = $tag->id;
                }
                $recipe->tags()->sync($tagIds);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Recipe updated successfully.',
                'data' => new RecipeResource($recipe->load(['categories', 'tags'])),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Recipe update failed', [
                'user_id' => $request->user()->id,
                'recipe_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while updating the recipe. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Recipe $recipe)
    {
        try {
            // $recipe = $request->user()->recipes()->findOrFail($id);
            $this->authorize('delete', $recipe);

            if ($recipe->image_path && Storage::disk('public')->exists($recipe->image_path)) {
                Storage::disk('public')->delete($recipe->image_path);
            }

            $recipe->categories()->detach();
            $recipe->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Recipe deleted successfully.',
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Recipe deletion failed', [
                'user_id' => $request->user()->id,
                'recipe_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while deleting the recipe. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
