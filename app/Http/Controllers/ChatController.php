<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userId     = Auth::id();
        $userMessage = $request->input('message');

        // Save user message
        Message::create([
            'user_id' => $userId,
            'role'    => 'user',
            'content' => $userMessage,
        ]);

        // Build conversation history
        $history = Message::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        // System prompt
        $systemPrompt = [
            'role'    => 'system',
            'content' => "You are MindEase, a highly trained, professional AI clinical therapist and mental health counselor.
                Your role is to conduct realistic, empathetic, and constructive therapy sessions with the user.
                
                Core directives:
                1. Professional Persona: Maintain the boundaries, tone, and pacing of a real human therapist. Do not act like a generic AI assistant. Speak directly, calmly, and analytically.
                2. Active Listening & Unpacking: Instead of just validating feelings and cheering the user up, ask deep, guiding, open-ended questions to help them unpack the root cause of their emotions.
                3. Therapeutic Frameworks: Gently apply evidence-based psychological frameworks where appropriate, such as Cognitive Behavioral Therapy (CBT) or Acceptance and Commitment Therapy (ACT), guiding the user to recognize cognitive distortions or re-align with their values.
                4. Tone: Empathetic, warm, but strictly clinical. Avoid toxic positivity, excessive emojis, or overly casual language.
                5. Pacing: Address one concept at a time. Keep responses concise and focused to avoid overwhelming the user. End with a single, clear question to prompt their reflection.
                6. Boundaries: Explicitly remind the user you are an AI if asked for a formal medical diagnosis or prescription, but remain engaged in exploring their cognitive patterns. Never offer medical advice.",
                
                
        ];

        // Call Groq API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'max_tokens' => 1024,
            'messages'   => array_merge([$systemPrompt], $history),
        ]);
        // Add this temporarily after the ->post() call
\Log::info('Groq response', [
    'status' => $response->status(),
    'body'   => $response->json()
]);

        // Handle errors
        if ($response->failed()) {
            return response()->json([
                'reply' => 'Sorry, I\'m having trouble connecting right now. Please try again in a moment. 💙'
            ], 200);
        }

        $aiReply = $response->json('choices.0.message.content')
            ?? 'I\'m here for you. Could you tell me more about how you\'re feeling?';

        // Save AI response
        Message::create([
            'user_id' => $userId,
            'role'    => 'assistant',
            'content' => $aiReply,
        ]);

        return response()->json(['reply' => $aiReply]);
    }

    public function clear()
    {
        Message::where('user_id', Auth::id())->delete();
        return redirect()->route('chat.index');
    }
}