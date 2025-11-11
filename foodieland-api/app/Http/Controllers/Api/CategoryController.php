<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RecipeResource;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $this->authorize('create', Category::class);
        $data = $request->validated();

        try {
            $category = Category::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug(Category::class, $data['name']),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully.',
                'data' => new CategoryResource($category),
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            Log::error('Category creation failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category),
        ], Response::HTTP_OK);
    }

    /**
     * Get recipes by category.
     */
    public function getRecipesByCategory(Category $category)
    {
        try {
            $recipes = $category->recipes()
                ->with(['user', 'categories'])
                ->latest()
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'message' => 'Recipes retrieved successfully.',
                'data' => RecipeResource::collection($recipes),
                'meta' => [
                    'category' => $category->name,
                    'total' => $recipes->total(),
                    'current_page' => $recipes->currentPage(),
                    'last_page' => $recipes->lastPage(),
                ],
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Failed to load recipes by category', [
                'category_id' => $category->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while fetching recipes for this category.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $data = $request->validated();

        try {
            $category->update([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug(Category::class, $data['name'], $category->id),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'data' => new CategoryResource($category),
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Category update failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
           
            $this->authorize('delete', $category);

            if ($category->recipes()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete category with assigned recipes.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully.',
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.',
            ], Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {
            Log::error('Category deletion failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate a unique slug
     */
    protected function generateUniqueSlug($model, $title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $i = 1;

        while ($model::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        return $slug;
    }
}
