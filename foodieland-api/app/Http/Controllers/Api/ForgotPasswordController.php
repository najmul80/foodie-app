<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Send OTP for password reset
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Rate limit: prevent spam
        if ($user->otp_expires_at && now()->lessThan($user->otp_expires_at->subMinutes(9))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please wait before requesting another OTP.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new RegisterOtpMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent to your email.',
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('Failed to send OTP', [
                'user_id' => $user->id,
                'error' => $th->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || $user->otp != $request->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP.',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully. You can now log in with your new password.',
        ], Response::HTTP_OK);
    }
}
