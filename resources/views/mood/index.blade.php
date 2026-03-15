@extends('layouts.app')
@section('title', 'Mood')

@section('head')
<style>
    .mood-page {
        height: 100vh;
        overflow-y: auto;
        padding: 2rem;
    }

    .mood-page::-webkit-scrollbar { width: 3px; }
    .mood-page::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 3px; }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-family: 'Merriweather', serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
    }

    .page-header p { font-size: 0.82rem; color: var(--muted); margin-top: 3px; }

    .mood-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        max-width: 860px;
    }

    @media (max-width: 640px) { .mood-grid { grid-template-columns: 1fr; } }

    .col-full { grid-column: 1 / -1; }

    .card-title {
        font-family: 'Merriweather', serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* MOOD PICKER */
    .mood-picker {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .mood-opt {
        flex: 1 1 calc(33.333% - 0.5rem);
        min-width: 60px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 0.85rem 0.4rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Inter', sans-serif;
    }
    
    .mood-opt:active { transform: scale(0.96); }

    .mood-opt .e { 
        font-size: 1.6rem; 
        display: block; 
        margin-bottom: 4px; 
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: bottom center;
        animation: wiggleEmoji 3s ease-in-out infinite;
    }
    
    /* Offset animation speeds so they don't look artificial and perfectly synced */
    .mood-opt:nth-child(1) .e { animation-delay: 0.1s; animation-duration: 3.1s; }
    .mood-opt:nth-child(2) .e { animation-delay: 0.3s; animation-duration: 2.9s; }
    .mood-opt:nth-child(3) .e { animation-delay: 0px;  animation-duration: 3.2s; }
    .mood-opt:nth-child(4) .e { animation-delay: 0.2s; animation-duration: 2.8s; }
    .mood-opt:nth-child(5) .e { animation-delay: 0.4s; animation-duration: 3.0s; }

    .mood-opt .l { font-size: 0.68rem; color: var(--muted); display: block; }

    .mood-opt:hover { border-color: var(--accent); background: var(--accent-lt); }
    .mood-opt:hover .e { transform: scale(1.15) translateY(-2px); animation-play-state: paused; }
    
    .mood-opt.selected { border-color: var(--accent); background: var(--accent-lt); }
    .mood-opt.selected .l { color: var(--accent); }
    .mood-opt.selected .e { 
        animation: floatSelected 2s ease-in-out infinite; 
    }

    @keyframes wiggleEmoji {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-8deg); }
        75% { transform: rotate(8deg); }
    }

    @keyframes floatSelected {
        0%, 100% { transform: translateY(0) scale(1.15); }
        50% { transform: translateY(-4px) scale(1.15); }
    }

    .note-area {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.7rem 0.9rem;
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        color: var(--text);
        resize: none;
        outline: none;
        margin-bottom: 1rem;
        transition: border-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1.5;
    }

    .note-area:focus { border-color: var(--accent); }
    .note-area::placeholder { color: var(--muted-2); }

    .today-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--accent-lt);
        border: 1px solid #c2d9c9;
        color: var(--accent);
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.78rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .strategies {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    @media (max-width: 480px) { .strategies { grid-template-columns: 1fr; } }


    .strategy {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.85rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
    }

    .strategy:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .strategy:active { transform: scale(0.98) translateY(0); box-shadow: none; }

    .s-icon { font-size: 1.2rem; margin-bottom: 0.35rem; }
    .s-title { font-size: 0.82rem; font-weight: 500; color: var(--text); margin-bottom: 0.2rem; }
    .s-desc { font-size: 0.75rem; color: var(--muted); line-height: 1.55; }

    /* CHART */
    .chart-wrap { height: 170px; position: relative; }

    /* HISTORY */
    .history-list { display: flex; flex-direction: column; gap: 0.5rem; }

    .history-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.65rem 0.85rem;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
    }

    .history-item:hover { border-color: var(--border-2); transform: translateX(2px); }
    .history-item:active { transform: scale(0.98) translateX(0); }

    .h-emoji { font-size: 1.3rem; flex-shrink: 0; }
    .h-info { flex: 1; min-width: 0; }
    .h-mood { font-size: 0.83rem; font-weight: 500; color: var(--text); text-transform: capitalize; }
    .h-date { font-size: 0.72rem; color: var(--muted); }
    .h-note { font-size: 0.73rem; color: var(--muted); font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .score-pips { display: flex; gap: 3px; flex-shrink: 0; }
    .pip { width: 7px; height: 7px; border-radius: 50%; background: var(--border-2); }
    .pip.on { background: var(--accent); }
</style>
@endsection

@section('content')
<div class="mood-page">
    <div class="page-header">
        <h1>Mood Tracker</h1>
        <p>Track how you feel over time.</p>
    </div>

    <div class="mood-grid">

        {{-- LOG MOOD --}}
        <div class="card">
            <div class="card-title">
                @if($todayMood) Today's Entry @else Log Your Mood @endif
            </div>

            @if($todayMood)
            <div class="today-badge">
                ✓ {{ \App\Models\Mood::moodEmoji($todayMood->mood) }}
                Logged as {{ ucfirst(str_replace('_', ' ', $todayMood->mood)) }}
            </div>
            @endif

            <form method="POST" action="{{ route('mood.store') }}">
                @csrf
                <input type="hidden" name="mood" id="mood-val">

                <div class="mood-picker">
                    @foreach(['very_sad' => ['😢','Very sad'], 'sad' => ['😕','Sad'], 'neutral' => ['😐','Okay'], 'happy' => ['🙂','Good'], 'very_happy' => ['😄','Great']] as $val => [$emoji, $label])
                    <div class="mood-opt" data-mood="{{ $val }}" onclick="pickMood(this)">
                        <span class="e">{{ $emoji }}</span>
                        <span class="l">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>

                <textarea class="note-area" name="note" rows="2" placeholder="Add a note (optional)..."></textarea>

                <button type="submit" id="log-btn" class="btn btn-primary" disabled style="width:100%;justify-content:center;">
                    Save Mood
                </button>
            </form>
        </div>

        {{-- COPING STRATEGIES --}}
        <div class="card">
            <div class="card-title">Coping Strategies</div>
            <div class="strategies">
                @foreach($copingStrategies as $s)
                <div class="strategy">
                    <div class="s-icon">{{ $s['icon'] }}</div>
                    <div class="s-title">{{ $s['title'] }}</div>
                    <div class="s-desc">{{ $s['desc'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- CHART --}}
        @if($chartData->count() > 1)
        <div class="card">
            <div class="card-title">Mood Over Time</div>
            <div class="chart-wrap">
                <canvas id="moodChart"></canvas>
            </div>
        </div>
        @endif

        {{-- HISTORY --}}
        @if($recentMoods->count() > 0)
        <div class="card">
            <div class="card-title">Recent Entries</div>
            <div class="history-list">
                @foreach($recentMoods as $m)
                <div class="history-item">
                    <div class="h-emoji">{{ \App\Models\Mood::moodEmoji($m->mood) }}</div>
                    <div class="h-info">
                        <div class="h-mood">{{ ucfirst(str_replace('_', ' ', $m->mood)) }}</div>
                        <div class="h-date">{{ $m->created_at->diffForHumans() }}</div>
                        @if($m->note)<div class="h-note">"{{ $m->note }}"</div>@endif
                    </div>
                    <div class="score-pips">
                        @for($i=1;$i<=5;$i++)
                        <div class="pip {{ $i <= $m->score ? 'on' : '' }}"></div>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
function pickMood(el) {
    document.querySelectorAll('.mood-opt').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('mood-val').value = el.dataset.mood;
    document.getElementById('log-btn').disabled = false;
}

@if($chartData->count() > 1)
const ctx = document.getElementById('moodChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! $chartData->pluck('date') !!},
        datasets: [{
            data: {!! $chartData->pluck('score') !!},
            borderColor: '#2d5a3d',
            backgroundColor: 'rgba(45,90,61,0.07)',
            borderWidth: 1.5,
            pointBackgroundColor: '#2d5a3d',
            pointRadius: 4,
            pointBorderWidth: 0,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                min: 1, max: 5,
                ticks: {
                    color: '#8a8780',
                    callback: v => ['','😢','😕','😐','🙂','😄'][v],
                    stepSize: 1,
                    font: { size: 11 }
                },
                grid: { color: '#f0ede8' }
            },
            x: {
                ticks: { color: '#8a8780', font: { size: 11 } },
                grid: { color: '#f0ede8' }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1a18',
                titleColor: '#f7f5f2',
                bodyColor: '#b5b2ac',
                padding: 10,
                callbacks: {
                    label: ctx => ['','😢 Very Sad','😕 Sad','😐 Okay','🙂 Good','😄 Great'][ctx.raw]
                }
            }
        }
    }
});
@endif
</script>
@endsection