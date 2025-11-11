<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new RegisterOtpMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful. An OTP has been sent to your email.',
                'user' => new UserResource($user),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Registration failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyOtp(Request $request)
    {
        // Rate limit to prevent brute-force attacks
        $key = 'verify-otp:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many attempts. Please try again later.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($key, 60); // Lockout for 60 seconds after 5 attempts

        // Validate input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        // Extra safety check (redundant but defensive)
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials or OTP.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Check OTP match
        if ((string)$user->otp !== $request->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials or OTP.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check OTP expiry
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update user verification status
        $user->update([
            'email_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        // Issue token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully. Your email is now verified.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], Response::HTTP_OK);

    }

    public function resendOtp(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Rate limit by IP to prevent abuse
        $rateKey = 'resend-otp:'.$request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests. Please try again later.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($rateKey, 60); // 5 attempts per minute

        // Retrieve user
        $user = User::where('email', $request->email)->first();

        // Defensive check (optional if validation is strict)
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to process request. Please check your email or try again later.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already verified.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Prevent frequent OTP resends (within 1 minute)
        if ($user->otp_expires_at && now()->lessThan($user->otp_expires_at->subMinutes(9))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please wait a moment before requesting another OTP.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Generate secure OTP
        $otp = rand(100000, 999999); // Or use: Str::random(6) for alphanumeric

        // Update user record
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email
        try {
            Mail::to($user->email)->send(new RegisterOtpMail($otp));

            Log::info('OTP resent successfully', ['user_id' => $user->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'A new OTP has been sent to your email.',
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Failed to resend OTP', [
                'user_id' => $user->id,
                'error' => $th->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        // Rate limit by IP address
        $rateKey = 'login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many login attempts. Please try again in a minute.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($rateKey, 60); // 5 attempts per 60 seconds

        // Attempt login
        if (! Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Retrieve authenticated user
        $user = Auth::guard('web')->user();

        // Check email verification
        if (is_null($user->email_verified_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your email before logging in.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Issue token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], Response::HTTP_OK);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Successfully logged out',
        ], Response::HTTP_OK);
    }
}
