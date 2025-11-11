<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request, Recipe $recipe)
    {
        $user = $request->user();
        
        // toggle() adds if not present, removes if present
        $result = $user->favoriteRecipes()->toggle($recipe->id);

        $status = count($result['attached']) > 0; // true if attached, false if detached

        return response()->json([
            'status' => 'success',
            'is_favorited' => $status,
        ]);
    }
}
