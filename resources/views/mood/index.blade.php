@extends('layouts.app')
@section('title', 'Mood tracker')

@section('head')
<style>
    .mood-page { flex: 1; min-height: 0; overflow-y: auto; padding: clamp(24px, 4vw, 56px); }
    .mood-content { width: 100%; max-width: 1080px; margin: 0 auto; }
    .mood-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 32px; }
    .mood-eyebrow { color: var(--accent); font-size: .7rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 10px; }
    .mood-header h1 { font-family: 'Merriweather', serif; font-size: clamp(1.65rem, 3vw, 2.1rem); font-weight: 400; letter-spacing: -.045em; }
    .mood-subtitle { color: var(--mood-muted); font-size: .85rem; line-height: 1.6; margin-top: 10px; }
    .mood-date { color: var(--mood-muted); font-size: .78rem; white-space: nowrap; padding-bottom: 5px; }
    .mood-page { --mood-muted: #69675f; }
    [data-theme="dark"] .mood-page { --mood-muted: #a7ad9d; }
    .mood-panels { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr); gap: 20px; }
    .mood-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 28px; min-width: 0; }
    .mood-panel h2 { font-size: .95rem; font-weight: 600; letter-spacing: -.02em; }
    .mood-panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 7px; }
    .mood-caption { color: var(--mood-muted); font-size: .76rem; line-height: 1.6; }
    .mood-period { color: var(--mood-muted); background: var(--bg); border: 1px solid var(--border); padding: 4px 9px; border-radius: 20px; font-size: .68rem; white-space: nowrap; }
    .mood-fieldset { border: 0; margin-top: 26px; min-width: 0; }
    .mood-fieldset legend { font-size: .85rem; font-weight: 500; margin-bottom: 14px; }
    .mood-choices { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 7px; }
    .mood-choice { position: relative; cursor: pointer; }
    .mood-choice input { position: absolute; width: 1px; height: 1px; opacity: 0; }
    .mood-choice-face { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; min-height: 83px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); transition: background-color 150ms ease, border-color 150ms ease; }
    .mood-choice-number { font-size: 1.1rem; font-weight: 500; font-variant-numeric: tabular-nums; color: var(--mood-muted); }
    .mood-choice-label { font-size: .66rem; white-space: nowrap; }
    @media (hover: hover) and (pointer: fine) { .mood-choice:hover .mood-choice-face { border-color: var(--border-2); background: var(--bg); } }
    .mood-choice input:checked + .mood-choice-face { border-color: var(--accent); background: var(--accent-lt); box-shadow: inset 0 0 0 1px var(--accent); }
    .mood-choice input:checked + .mood-choice-face .mood-choice-number { color: var(--accent); }
    .mood-choice input:focus-visible + .mood-choice-face { outline: 2px solid var(--accent); outline-offset: 3px; }
    .mood-note-label { display: flex; gap: 6px; font-size: .78rem; font-weight: 500; margin: 24px 0 10px; }
    .mood-note-label span { font-weight: 400; color: var(--mood-muted); }
    .mood-note { display: block; width: 100%; min-height: 94px; padding: 12px 14px; background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 9px; font: inherit; font-size: .8rem; line-height: 1.65; resize: vertical; }
    .mood-note::placeholder { color: var(--mood-muted); }
    .mood-note:focus-visible, .mood-save:focus-visible, .mood-pagination a:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }
    .mood-save { width: 100%; margin-top: 18px; min-height: 44px; border: 0; border-radius: 9px; background: var(--accent); color: #fff; font: inherit; font-size: .8rem; font-weight: 500; cursor: pointer; transition: opacity 150ms ease, transform 150ms ease; }
    [data-theme="dark"] .mood-save { color: #111410; }
    @media (hover: hover) and (pointer: fine) { .mood-save:hover { opacity: .9; } }
    .mood-save:active { transform: scale(.98); }
    .mood-save:disabled { opacity: .6; cursor: wait; }
    .mood-error { font-size: .76rem; color: var(--danger); margin-top: 8px; }
    .mood-chart { width: 100%; height: auto; display: block; margin-top: 26px; overflow: visible; }
    .mood-chart text { fill: var(--mood-muted); font-family: 'Inter', sans-serif; font-size: 11px; }
    .mood-chart-grid { stroke: var(--border); stroke-dasharray: 3 5; }
    .mood-chart-line { stroke: var(--accent); stroke-width: 2; fill: none; }
    .mood-chart-point { fill: var(--accent); stroke: var(--surface); stroke-width: 2; }
    .mood-chart-foot { display: flex; align-items: center; gap: 7px; margin-top: 18px; font-size: .72rem; color: var(--mood-muted); line-height: 1.6; }
    .mood-key { width: 7px; height: 7px; flex-shrink: 0; background: var(--accent); border-radius: 50%; }
    .mood-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 225px; padding: 24px 12px; text-align: center; }
    .mood-empty-mark { width: 36px; height: 28px; display: flex; align-items: flex-end; gap: 5px; margin-bottom: 18px; }
    .mood-empty-mark i { flex: 1; background: var(--accent-lt); border: 1px solid var(--border); border-radius: 3px; }
    .mood-empty-mark i:nth-child(1) { height: 12px; }
    .mood-empty-mark i:nth-child(2) { height: 21px; }
    .mood-empty-mark i:nth-child(3) { height: 28px; }
    .mood-empty h3 { font-size: .85rem; font-weight: 500; margin-bottom: 7px; }
    .mood-history { margin-top: 28px; }
    .mood-history .mood-panel-heading { margin-bottom: 20px; }
    .mood-history-list { list-style: none; }
    .mood-entry { display: grid; grid-template-columns: 130px 95px minmax(0, 1fr) 38px; align-items: start; gap: 20px; padding: 19px 0; border-top: 1px solid var(--border); }
    .mood-entry time { display: flex; flex-direction: column; gap: 6px; font-size: .78rem; }
    .mood-entry time span { color: var(--mood-muted); font-size: .7rem; }
    .mood-entry-label { font-size: .8rem; font-weight: 500; }
    .mood-entry-note { font-size: .78rem; line-height: 1.65; color: var(--mood-muted); white-space: pre-wrap; overflow-wrap: anywhere; }
    .mood-entry-score { text-align: right; font-size: .75rem; font-variant-numeric: tabular-nums; color: var(--mood-muted); }
    .mood-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 18px; border-top: 1px solid var(--border); color: var(--mood-muted); font-size: .75rem; }
    .mood-pagination a { color: var(--accent); text-decoration: none; min-height: 40px; display: inline-flex; align-items: center; }
    .mood-timezone { color: var(--mood-muted); font-size: .68rem; margin-top: 20px; }
    .mood-data { margin-top: 16px; font-size: .72rem; color: var(--mood-muted); }
    .mood-data summary { cursor: pointer; padding: 8px 0; }
    .mood-data table { width: 100%; border-collapse: collapse; text-align: left; }
    .mood-data td, .mood-data th { padding: 7px 0; border-bottom: 1px solid var(--border); font-weight: 400; }
    @media (max-width: 1050px) { .mood-panels { grid-template-columns: 1fr; } }
    @media (max-width: 540px) {
        .mood-page { padding: 26px 16px; }
        .mood-header { display: block; margin-bottom: 24px; }
        .mood-date { margin-top: 14px; }
        .mood-panel { padding: 22px 18px; border-radius: 12px; }
        .mood-entry { grid-template-columns: 1fr auto; gap: 10px 16px; }
        .mood-entry time { grid-row: 1; }
        .mood-entry-label { grid-column: 1; grid-row: 2; }
        .mood-entry-score { grid-column: 2; grid-row: 1; }
        .mood-entry-note { grid-column: 1 / -1; }
        .mood-choices { gap: 5px; }
        .mood-choice-label { font-size: .63rem; }
        .mood-chart text { font-size: 14px; }
    }
    @media (prefers-reduced-motion: reduce) { .mood-page *, .mood-page *::after { transition: none !important; } }
</style>
@endsection

@section('content')
<div class="mood-page">
    <div class="mood-content">
        <header class="mood-header">
            <div>
                <p class="mood-eyebrow">A moment for yourself</p>
                <h1>Mood tracker</h1>
                <p class="mood-subtitle">Notice how you feel, one check-in at a time.</p>
            </div>
            <p class="mood-date">{{ $today->format('l, F j') }}</p>
        </header>

        <div class="mood-panels">
            <section class="mood-panel" aria-labelledby="check-in-title">
                <div class="mood-panel-heading"><h2 id="check-in-title">Your check-in</h2></div>
                <p class="mood-caption">@if($todayMood) Last today: {{ \App\Models\Mood::moodLabel($todayMood->mood) }} at {{ $todayMood->created_at->copy()->timezone($timezone)->format('g:i a') }}. @else Take a moment to check in with yourself. @endif</p>
                <form method="POST" action="{{ route('mood.store') }}" id="mood-form">
                    @csrf
                    <input type="hidden" name="timezone" value="{{ $timezone }}" id="mood-timezone">
                    <fieldset class="mood-fieldset" @error('mood') aria-describedby="mood-error" @enderror>
                        <legend>How do you feel right now?</legend>
                        <div class="mood-choices">
                            @foreach(\App\Models\Mood::OPTIONS as $value => $option)
                                <label class="mood-choice">
                                    <input type="radio" name="mood" value="{{ $value }}" required @checked(old('mood') === $value)>
                                    <span class="mood-choice-face">
                                        <span class="mood-choice-number" aria-hidden="true">{{ $option['score'] }}</span>
                                        <span class="mood-choice-label">{{ $option['label'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    @error('mood') <p class="mood-error" id="mood-error" role="alert">{{ $message }}</p> @enderror
                    <label for="mood-note" class="mood-note-label">A little context <span>(optional)</span></label>
                    <textarea name="note" id="mood-note" class="mood-note" rows="3" maxlength="500" placeholder="What's on your mind?" @error('note') aria-invalid="true" aria-describedby="note-error" @enderror>{{ old('note') }}</textarea>
                    @error('note') <p class="mood-error" id="note-error" role="alert">{{ $message }}</p> @enderror
                    @error('timezone') <p class="mood-error" role="alert">{{ $message }}</p> @enderror
                    <button type="submit" class="mood-save" id="mood-save">Save check-in</button>
                </form>
            </section>

            <section class="mood-panel" aria-labelledby="trend-title">
                <div class="mood-panel-heading">
                    <h2 id="trend-title">Mood over time</h2><span class="mood-period">Last 14 days</span>
                </div>
                <p class="mood-caption">{{ $recordedDays }} of 14 days recorded</p>
                @if($recordedDays)
                    <svg class="mood-chart" viewBox="0 0 490 230" role="img" aria-labelledby="chart-title chart-description">
                        <title id="chart-title">Daily mood averages over the last 14 days</title>
                        <desc id="chart-description">Scores range from 1, very low, to 5, great. Missing days are left blank. The values are also available in the data table below.</desc>
                        @foreach([5 => 'Great', 4 => 'Good', 3 => 'Okay', 2 => 'Low', 1 => 'Very low'] as $score => $label)
                            @php($y = 20 + (5 - $score) * 43)
                            <text x="0" y="{{ $y + 4 }}">{{ $label }}</text>
                            <line class="mood-chart-grid" x1="65" x2="478" y1="{{ $y }}" y2="{{ $y }}" />
                        @endforeach
                        @foreach($chartData as $i => $point)
                            @php($x = 70 + $i * 31)
                            @if($point['score'] !== null)
                                @php($y = 20 + (5 - $point['score']) * 43)
                                @if($i > 0 && $chartData[$i - 1]['score'] !== null)
                                    <line class="mood-chart-line" x1="{{ $x - 31 }}" y1="{{ 20 + (5 - $chartData[$i - 1]['score']) * 43 }}" x2="{{ $x }}" y2="{{ $y }}" />
                                @endif
                                <circle class="mood-chart-point" cx="{{ $x }}" cy="{{ $y }}" r="4.5"><title>{{ $point['label'] }}: {{ $point['score'] }}/5 from {{ $point['count'] }} {{ Str::plural('check-in', $point['count']) }}</title></circle>
                            @endif
                            @if(in_array($i, [0, 4, 9, 13]))
                                <text x="{{ $x }}" y="221" text-anchor="{{ $i === 13 ? 'end' : 'middle' }}">{{ $point['label'] }}</text>
                            @endif
                        @endforeach
                    </svg>
                    <p class="mood-chart-foot"><span class="mood-key" aria-hidden="true"></span>Daily average of your check-ins. Gaps mean no entry.</p>
                    <details class="mood-data">
                        <summary>View daily values</summary>
                        <table>
                            <caption class="mood-caption">{{ $chartData->first()['label'] }} to {{ $chartData->last()['label'] }}</caption>
                            <thead><tr><th scope="col">Date</th><th scope="col">Average / 5</th><th scope="col">Check-ins</th></tr></thead>
                            <tbody>@foreach($chartData as $point)<tr><th scope="row">{{ $point['label'] }}</th><td>{{ $point['score'] ?? 'No entry' }}</td><td>{{ $point['count'] }}</td></tr>@endforeach</tbody>
                        </table>
                    </details>
                @else
                    <div class="mood-empty">
                        <div class="mood-empty-mark" aria-hidden="true"><i></i><i></i><i></i></div>
                        <h3>A clearer picture starts here</h3>
                        <p class="mood-caption">Save your first check-in to begin your timeline.</p>
                    </div>
                @endif
            </section>
        </div>

        <section class="mood-panel mood-history" aria-labelledby="history-title">
            <div class="mood-panel-heading"><h2 id="history-title">Check-in history</h2><span class="mood-caption">{{ $recentMoods->total() }} {{ Str::plural('entry', $recentMoods->total()) }}</span></div>
            @if($recentMoods->isNotEmpty())
                <ol class="mood-history-list">
                    @foreach($recentMoods as $entry)
                        <li class="mood-entry">
                            <time datetime="{{ $entry->created_at->toIso8601String() }}">{{ $entry->created_at->copy()->timezone($timezone)->format('M j, Y') }}<span>{{ $entry->created_at->copy()->timezone($timezone)->format('g:i a') }}</span></time>
                            <span class="mood-entry-label">{{ \App\Models\Mood::moodLabel($entry->mood) }}</span>
                            <p class="mood-entry-note">{{ $entry->note ?? '—' }}</p>
                            <span class="mood-entry-score" aria-label="{{ $entry->score }} out of 5">{{ $entry->score }}/5</span>
                        </li>
                    @endforeach
                </ol>
                @if($recentMoods->hasPages())
                    <nav class="mood-pagination" aria-label="Check-in history pages">
                        @if($recentMoods->onFirstPage()) <span>Newer</span> @else <a href="{{ $recentMoods->previousPageUrl() }}">Newer</a> @endif
                        <span>Page {{ $recentMoods->currentPage() }} of {{ $recentMoods->lastPage() }}</span>
                        @if($recentMoods->hasMorePages()) <a href="{{ $recentMoods->nextPageUrl() }}">Older</a> @else <span>Older</span> @endif
                    </nav>
                @endif
            @else
                <p class="mood-caption">Your saved check-ins will appear here.</p>
            @endif
        </section>
        <p class="mood-timezone">Times shown in {{ str_replace('_', ' ', $timezone) }}.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const timezoneInput = document.getElementById('mood-timezone');
        const url = new URL(window.location.href);
        // Resolve local calendar dates on the server as well as in the form.
        if (timezone && timezone !== timezoneInput.value && !url.searchParams.has('timezone')) {
            url.searchParams.set('timezone', timezone);
            window.location.replace(url);
            return;
        }
        if (timezone) timezoneInput.value = timezone;
        document.getElementById('mood-form').addEventListener('submit', () => {
            const button = document.getElementById('mood-save');
            button.disabled = true;
            button.textContent = 'Saving...';
        });
    })();
</script>
@endsection
