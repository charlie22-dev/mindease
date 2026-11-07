<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoodController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['timezone' => 'nullable|timezone']);
        $timezone = $validated['timezone'] ?? $request->session()->get('mood_timezone', config('app.timezone'));
        $request->session()->put('mood_timezone', $timezone);

        $today = CarbonImmutable::now($timezone)->startOfDay();
        $start = $today->subDays(13);
        $entries = Mood::where('user_id', $request->user()->id)
            ->where('created_at', '>=', $start->utc())
            ->where('created_at', '<', $today->addDay()->utc())
            ->orderBy('created_at')->orderBy('id')->get();

        $dailyEntries = $entries->groupBy(fn (Mood $entry) => $entry->created_at->copy()->timezone($timezone)->toDateString());
        $todayMood = $dailyEntries->get($today->toDateString())?->last();
        $chartData = collect(range(0, 13))->map(function ($offset) use ($start, $dailyEntries) {
            $day = $start->addDays($offset);
            $dayEntries = $dailyEntries->get($day->toDateString(), collect());

            return [
                'date' => $day->toDateString(),
                'label' => $day->format('M j'),
                'score' => $dayEntries->isEmpty() ? null : round($dayEntries->avg('score'), 2),
                'count' => $dayEntries->count(),
            ];
        });

        $recentMoods = Mood::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString();
        $recordedDays = $dailyEntries->count();

        return view('mood.index', compact('todayMood', 'recentMoods', 'chartData', 'recordedDays', 'timezone', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mood' => ['required', Rule::in(array_keys(Mood::OPTIONS))],
            'note' => 'nullable|string|max:500',
            'timezone' => 'nullable|timezone',
        ]);

        Mood::create([
            'user_id' => $request->user()->id,
            'mood' => $validated['mood'],
            'score' => Mood::moodScore($validated['mood']),
            'note' => $validated['note'] ?? null,
        ]);

        if (isset($validated['timezone'])) {
            $request->session()->put('mood_timezone', $validated['timezone']);
        }

        return redirect()->route('mood.index')->with('success', 'Check-in saved.');
    }
}
