<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\BlogStoreRequest;
use App\Http\Requests\Blog\BlogUpdateRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = BlogPost::with(['user', 'categories', 'tags'])->latest()->paginate(10);
        return BlogPostResource::collection($blogs)
            ->additional([
                'status' => 'success',
                'message' => $blogs->isEmpty() ? 'No blog posts found.' : 'Blog posts retrieved successfully.',
            ]);
        // Return success even if empty (frontend handles empty list nicely)
        // return response()->json([
        //     'status' => 'success',
        //     'message' => $blogs->isEmpty() ? 'No blogs found.' : 'Blogs retrieved successfully.',
        //     'data' => BlogPostResource::collection($blogs),
        //     'meta' => [
        //         'total' => $blogs->total(),
        //         'per_page' => $blogs->perPage(),
        //         'current_page' => $blogs->currentPage(),
        //         'last_page' => $blogs->lastPage(),
        //     ],
        // ], Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogStoreRequest $request)
    {
        $this->authorize('create', BlogPost::class);

        $data = $request->validated();

        try {
            // Image handling (single, consistent path)
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
                            .'-'.time().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog_posts', $filename, 'public');
            }

            // Unique slug generation
            $baseSlug = Str::slug($data['title']);
            $slug = $baseSlug;
            $i = 1;
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$i++;
            }

            $post = $request->user()->blogPosts()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'image_path' => $imagePath,
            ]);

            if (! empty($data['category_ids'])) {
                $post->categories()->sync($data['category_ids']);
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
                $post->tags()->sync($tagIds);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Blog post created successfully.',
                'data' => new BlogPostResource($post->load(['user', 'categories', 'tags'])),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Blog post creation failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create blog post. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {

        $this->authorize('view', $blogPost);

        return response()->json([
            'status' => 'success',
            'data' => new BlogPostResource($blogPost->load(['user', 'categories', 'tags', 'comments.user'])),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogUpdateRequest $request, BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);

        try {
            $data = $request->validated();

            // If title changed, regenerate unique slug
            if (isset($data['title']) && $data['title'] !== $blogPost->title) {
                $baseSlug = Str::slug($data['title']);
                $slug = $baseSlug;
                $i = 1;
                while (BlogPost::where('slug', $slug)->where('id', '!=', $blogPost->id)->exists()) {
                    $slug = $baseSlug.'-'.$i++;
                }
                $data['slug'] = $slug;
            }

            // Image update (consistent folder)
            if ($request->hasFile('image')) {
                if ($blogPost->image_path && Storage::disk('public')->exists($blogPost->image_path)) {
                    Storage::disk('public')->delete($blogPost->image_path);
                }
                $image = $request->file('image');
                $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
                            .'-'.time().'.'.$image->getClientOriginalExtension();
                $data['image_path'] = $image->storeAs('blog_posts', $filename, 'public');
            }

            $blogPost->update($data);

            if ($request->has('category_ids')) {
                $blogPost->categories()->sync($data['category_ids']);
            }

            if ($request->has('tags')) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug(trim($tagName))],
                        ['name' => ucwords(trim($tagName))]
                    );
                    $tagIds[] = $tag->id;
                }
                $blogPost->tags()->sync($tagIds);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Blog post updated successfully.',
                'data' => new BlogPostResource($blogPost->load(['user', 'categories', 'tags'])),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Blog post update failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => $request->user()?->id,
                'post_id' => $blogPost->id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update blog post. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $this->authorize('delete', $blogPost);

        try {
            $blogPost->categories()->detach();
            $blogPost->tags()->detach();

            if ($blogPost->image_path && Storage::disk('public')->exists($blogPost->image_path)) {
                Storage::disk('public')->delete($blogPost->image_path);
            }

            $blogPost->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            Log::error('Blog post deletion failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'post_id' => $blogPost->id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete blog post. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
