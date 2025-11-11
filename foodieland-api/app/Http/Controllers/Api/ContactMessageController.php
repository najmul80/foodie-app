<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $contactMessage = ContactMessage::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Your message has been sent successfully!',
                'data' => $contactMessage,
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            Log::error('Failed to save contact message', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'payload' => $validated,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send your message. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
