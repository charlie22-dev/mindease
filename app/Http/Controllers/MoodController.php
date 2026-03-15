<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoodController extends Controller
{
    public function index()
    {
        $todayMood = Mood::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $recentMoods = Mood::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        // Build chart data (last 7 days)
        $chartData = Mood::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->take(14)
            ->get()
            ->map(fn($m) => [
                'date'  => $m->created_at->format('M d'),
                'score' => $m->score,
                'mood'  => $m->mood,
                'emoji' => Mood::moodEmoji($m->mood),
            ]);

        $copingStrategies = $this->getCopingStrategies($todayMood?->mood ?? 'neutral');

        return view('mood.index', compact('todayMood', 'recentMoods', 'chartData', 'copingStrategies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mood' => 'required|in:very_sad,sad,neutral,happy,very_happy',
            'note' => 'nullable|string|max:500',
        ]);

        Mood::create([
            'user_id' => Auth::id(),
            'mood'    => $request->mood,
            'score'   => Mood::moodScore($request->mood),
            'note'    => $request->note,
        ]);

        return redirect()->route('mood.index')->with('success', 'Mood logged successfully!');
    }

    private function getCopingStrategies(string $mood): array
    {
        $strategies = [
            'very_sad' => [
                ['icon' => '🫁', 'title' => 'Box Breathing',       'desc' => 'Inhale 4s → Hold 4s → Exhale 4s → Hold 4s. Repeat 4 times.'],
                ['icon' => '📝', 'title' => 'Journaling',          'desc' => 'Write down 3 things you feel right now, no filter needed.'],
                ['icon' => '🤝', 'title' => 'Reach Out',           'desc' => 'Text a trusted friend or call a helpline. You don\'t have to face this alone.'],
                ['icon' => '🛁', 'title' => 'Self-Care Ritual',    'desc' => 'Warm shower, comfy clothes, favorite drink. Small comforts matter.'],
            ],
            'sad' => [
                ['icon' => '🚶', 'title' => '10-Min Walk',         'desc' => 'A short walk outside can shift your mood significantly.'],
                ['icon' => '🎵', 'title' => 'Music Therapy',       'desc' => 'Create a playlist of songs that uplift you.'],
                ['icon' => '🧸', 'title' => 'Comfort Activity',    'desc' => 'Do something you loved as a child — draw, build, cook.'],
                ['icon' => '📞', 'title' => 'Talk to Someone',     'desc' => 'Share how you feel with someone you trust.'],
            ],
            'neutral' => [
                ['icon' => '🧘', 'title' => '5-Min Meditation',   'desc' => 'Sit quietly, focus on your breath for 5 minutes.'],
                ['icon' => '🎯', 'title' => 'Set One Goal',        'desc' => 'Pick one small, achievable goal for today.'],
                ['icon' => '🌿', 'title' => 'Spend Time in Nature','desc' => 'Even a few minutes outside can refresh your mind.'],
                ['icon' => '📚', 'title' => 'Learn Something New', 'desc' => 'Read an article or watch a short video on a topic you enjoy.'],
            ],
            'happy' => [
                ['icon' => '💪', 'title' => 'Channel Your Energy', 'desc' => 'Use this good mood to tackle something you\'ve been putting off.'],
                ['icon' => '💌', 'title' => 'Spread the Joy',      'desc' => 'Send a kind message to someone you care about.'],
                ['icon' => '🗒️', 'title' => 'Gratitude Log',       'desc' => 'Write down what made you happy today to revisit later.'],
                ['icon' => '🎨', 'title' => 'Create Something',    'desc' => 'Happy moods fuel creativity — draw, write, or build.'],
            ],
            'very_happy' => [
                ['icon' => '🌟', 'title' => 'Celebrate!',          'desc' => 'You\'re doing great! Take a moment to acknowledge your wins.'],
                ['icon' => '🏋️', 'title' => 'Physical Activity',   'desc' => 'Burn that positive energy with exercise or dance.'],
                ['icon' => '🎁', 'title' => 'Give Back',           'desc' => 'Help someone else or do a random act of kindness.'],
                ['icon' => '📖', 'title' => 'Document This',       'desc' => 'Write about what\'s making you so happy — future you will thank you.'],
            ],
        ];

        return $strategies[$mood] ?? $strategies['neutral'];
    }
}