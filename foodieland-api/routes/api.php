<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES ---

// SOCIAL LOGIN ROUTES
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback']);

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPasswordWithOtp']);

// Authors
Route::get('/authors/{user}', [AuthorController::class, 'show']);
Route::get('/authors/{user}/recipes', [AuthorController::class, 'getRecipes']);
Route::get('/authors/{user}/blogs', [AuthorController::class, 'getBlogs']);

// Settings
Route::get('/settings/{key}', [SettingsController::class, 'show']);

// contact
Route::post('/contact', [ContactMessageController::class, 'store']);

// Recipes
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show']);

// Blog Posts
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{blogPost:slug}', [BlogController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}/recipes', [CategoryController::class, 'getRecipesByCategory']);

// --- AUTHENTICATED ROUTES ---

Route::middleware('auth:sanctum')->group(function () {
    // User Management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    Route::post('/user/profile', [ProfileController::class, 'profileUpdate']);

    // Site Management
    Route::post('/settings/{key}', [SettingsController::class, 'update']);
    Route::post('/recipes/{recipe:slug}/favorite', [FavoriteController::class, 'toggleFavorite']);
    // Recipe Management
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{recipe:slug}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy']);

    // Blog Post Management
    Route::post('/blog', [BlogController::class, 'store']);
    Route::post('/blog/{blogPost:slug}', [BlogController::class, 'update']);
    Route::delete('/blog/{blogPost:slug}', [BlogController::class, 'destroy']);

    // Comment Management
    Route::post('/recipes/{recipe:slug}/comments', [CommentController::class, 'storeForRecipe']);
    Route::post('/blog/{blogPost:slug}/comments', [CommentController::class, 'storeForBlog']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

// --- ADMIN ONLY ROUTES ---

Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    // Category Management
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});
