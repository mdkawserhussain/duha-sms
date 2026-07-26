<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($request) {
                $q->where('sender_id', $request->user()->id)
                    ->orWhere('receiver_id', $request->user()->id);
            });

        if ($inbox = $request->input('inbox')) {
            $query->where('receiver_id', $request->user()->id);
        }

        if ($sent = $request->input('sent')) {
            $query->where('sender_id', $request->user()->id);
        }

        $messages = $query->latest()->paginate(20);

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $validated['sender_id'] = $request->user()->id;

        $message = Message::create($validated);

        return response()->json($message->load(['sender', 'receiver']), 201);
    }

    public function show(Message $message): JsonResponse
    {
        $message->load(['sender', 'receiver']);

        if ($message->receiver_id === request()->user()->id && ! $message->is_read) {
            $message->markAsRead();
        }

        return response()->json($message);
    }

    public function markAsRead(Message $message): JsonResponse
    {
        $message->markAsRead();

        return response()->json(['message' => 'Message marked as read']);
    }
}
