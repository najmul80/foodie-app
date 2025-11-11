<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectToProvider($provider)
    {
        // This generates the URL for the frontend to open
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $url]);
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            // If the provider returns an error, redirect to the frontend with an error
            return redirect(env('FRONTEND_URL') . '/login?error=Unable to login using ' . $provider . '. Please try again.');
        }

        // Find or create the user
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Create a random password
                'email_verified_at' => now(), // Social accounts are pre-verified
            ]);
            $user->assignRole('User');
        }

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Redirect the user back to the frontend with the token and user data
        $userJson = json_encode(new UserResource($user));
        return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $token . '&user=' . urlencode($userJson));
    }
}
