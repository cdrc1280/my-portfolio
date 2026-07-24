<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\Ai\AiRagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiChatController extends Controller
{
    protected AiRagService $ragService;

    public function __construct(AiRagService $ragService)
    {
        $this->ragService = $ragService;
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $sessionId = $validated['session_id'] ?? Str::uuid()->toString();

        $conversation = AiConversation::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'title' => Str::limit($validated['message'], 40),
                'ip_address' => $request->ip(),
            ]
        );

        // Store user message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        // Get past messages history
        $history = $conversation->messages()
            ->take(10)
            ->get()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        // Ask RAG AI Engine
        $result = $this->ragService->ask($validated['message'], $history);

        // Store assistant response
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $result['reply'],
            'suggested_actions' => $result['suggested_actions'],
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'reply' => $result['reply'],
            'suggested_actions' => $result['suggested_actions'],
        ]);
    }
}
