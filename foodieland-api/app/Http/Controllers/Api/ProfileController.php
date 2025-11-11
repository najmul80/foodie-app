<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // public function profileUpdate(Request $request)
    // {
    //     $user = $request->user();

    //     $validatedData = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'email' => [
    //             'sometimes',
    //             'required',
    //             'string',
    //             'email',
    //             'max:255',
    //             Rule::unique('users')->ignore($user->id),
    //         ],
    //         'bio' => 'nullable|string|max:1000',
    //         'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     if ($request->hasFile('profile_image')) {
    //         // Delete old profile image if it exists
    //         if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
    //             Storage::disk('public')->delete($user->profile_image);
    //         }
    //         // Store the new image and get its path
    //         $path = $request->file('profile_image')->store('profiles', 'public');
    //         $validatedData['profile_image'] = $path;
    //     }

    //     // Update user with validated data
    //     $user->update($validatedData);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Profile updated successfully.',
    //         'data' => new UserResource($user),
    //     ]);
    // }


    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // ===============================================
            // THIS IS THE LINE THAT IS FIXED
            // ===============================================
            $path = $request->file('profile_image')->store('profile-images', 'public');
            
            $validatedData['profile_image'] = $path;
        }

        $user->update($validatedData);

        // Return the UserResource to ensure the profile_image_url is generated
        return new UserResource($user);
    }
}
