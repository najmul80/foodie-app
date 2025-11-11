<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * List all comments for a Recipe (paginated) 
     */
    public function indexForRecipe(Recipe $recipe)
    {
        $comments = $recipe->comments()->with('user')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => CommentResource::collection($comments),
        ], Response::HTTP_OK);
    }

    /**
     * List all comments for a BlogPost (paginated)
     */
    public function indexForBlog(BlogPost $blogPost)
    {
        $comments = $blogPost->comments()->with('user')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => CommentResource::collection($comments),
        ], Response::HTTP_OK);
    }

    /**
     * Store a comment for a Recipe
     */
    public function storeForRecipe(Request $request, Recipe $recipe)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $recipe->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully.',
            'data' => new CommentResource($comment->load('user')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Store a comment for a BlogPost
     */
    public function storeForBlog(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $blogPost->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully.',
            'data' => new CommentResource($comment->load('user')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $request->body,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully.',
            'data' => new CommentResource($comment->load('user')),
        ], Response::HTTP_OK);
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully.',
        ], Response::HTTP_OK);
    }
}
