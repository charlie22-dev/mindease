<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GuestChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'array|max:20',
        ]);

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

        $history = collect($request->history ?? [])
            ->filter(fn($m) => in_array($m['role'] ?? '', ['user', 'assistant']))
            ->map(fn($m) => ['role' => $m['role'], 'content' => substr($m['content'], 0, 500)])
            ->values()
            ->toArray();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'      => 'llama-3.3-70b-versatile',
            'max_tokens' => 512,
            'messages'   => array_merge([$systemPrompt], $history),
        ]);

        if ($response->failed()) {
            return response()->json(['reply' => 'I\'m having trouble connecting right now. Please try again 💙']);
        }

        $reply = $response->json('choices.0.message.content')
            ?? 'I\'m here for you. Could you tell me more?';

        return response()->json(['reply' => $reply]);
    }
}    