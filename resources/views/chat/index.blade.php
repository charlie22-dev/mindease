@extends('layouts.app')
@section('title', 'Chat')

@section('head')
<style>
    .chat-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0;
    }

    /* TOP BAR */
    .chat-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        flex-shrink: 0;
    }

    .chat-topbar-left h1 {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 500;
        color: var(--text);
    }

    .chat-topbar-left p {
        font-size: 0.78rem;
        color: var(--muted);
        margin-top: 1px;
    }

    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        color: var(--accent);
        background: var(--accent-lt);
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
    }

    .status-dot::before {
        content: '';
        width: 6px;
        height: 6px;
        background: var(--accent);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* MESSAGES */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        scroll-behavior: smooth;
    }

    .chat-messages::-webkit-scrollbar { width: 3px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 3px; }

    /* EMPTY */
    .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        text-align: center;
        animation: fadeIn 0.5s ease;
    }

    .empty-icon {
        width: 60px;
        height: 60px;
        background: var(--accent-lt);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }

    .empty-state h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        color: var(--text);
        font-weight: 500;
    }

    .empty-state p {
        font-size: 0.85rem;
        color: var(--muted);
        max-width: 260px;
        line-height: 1.7;
    }

    .starters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 0.5rem;
    }

    .starter-chip {
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--muted);
        padding: 0.4rem 0.85rem;
        border-radius: 20px;
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Inter', sans-serif;
    }

    .starter-chip:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-lt);
        transform: translateY(-1px);
    }
    .starter-chip:active { transform: scale(0.96) translateY(0); }

    /* BUBBLES */
    .msg-row {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
        animation: fadeUp 0.25s ease;
        max-width: 680px;
    }

    .msg-row.user {
        flex-direction: row-reverse;
        align-self: flex-end;
    }

    .msg-row.ai { align-self: flex-start; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .msg-avatar {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    .msg-avatar.ai-av {
        background: var(--accent-lt);
        border: 1px solid #c2d9c9;
    }

    .msg-avatar.user-av {
        background: var(--user-bg);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .msg-body { display: flex; flex-direction: column; gap: 3px; }

    .msg-row.user .msg-body { align-items: flex-end; }

    .bubble {
        padding: 0.75rem 1rem;
        border-radius: 14px;
        font-size: 0.9rem;
        line-height: 1.65;
        max-width: 480px;
    }

    .bubble.ai-bubble {
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text);
        border-bottom-left-radius: 4px;
        box-shadow: var(--shadow);
    }

    .bubble.user-bubble {
        background: var(--user-bg);
        color: var(--user-text);
        border-bottom-right-radius: 4px;
    }

    .msg-time {
        font-size: 0.68rem;
        color: var(--muted-2);
        padding: 0 2px;
    }

    /* TYPING */
    .typing-row { display: none; }
    .typing-row.show { display: flex; }

    .typing-bubble {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        border-bottom-left-radius: 4px;
        padding: 0.75rem 1rem;
        display: flex;
        gap: 4px;
        align-items: center;
        box-shadow: var(--shadow);
    }

    .typing-bubble span {
        width: 5px;
        height: 5px;
        background: var(--muted-2);
        border-radius: 50%;
        animation: typingBounce 1.3s infinite;
    }

    .typing-bubble span:nth-child(2) { animation-delay: 0.15s; }
    .typing-bubble span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes typingBounce {
        0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
        40% { transform: translateY(-5px); opacity: 1; }
    }

    /* INPUT */
    .chat-input-bar {
        padding: 1.25rem 2rem;
        border-top: 1px solid var(--border);
        background: var(--surface);
        flex-shrink: 0;
    }

    .input-row {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
        max-width: 680px;
        margin: 0 auto;
    }

    .input-field-wrap {
        flex: 1;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.7rem 1rem;
        transition: border-color 0.15s;
    }

    .input-field-wrap:focus-within { border-color: var(--accent); }

    #msg-input {
        width: 100%;
        background: none;
        border: none;
        outline: none;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: var(--text);
        resize: none;
        max-height: 100px;
        line-height: 1.5;
    }

    #msg-input::placeholder { color: var(--muted-2); }

    #send-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--accent);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
        font-size: 1rem;
        outline: none;
    }

    #send-btn:hover { background: #235030; transform: translateY(-1.5px); box-shadow: 0 4px 12px rgba(45,90,61,0.25); }
    #send-btn:active { transform: scale(0.92) translateY(0); box-shadow: none; }
    #send-btn:disabled { opacity: 0.35; cursor: not-allowed; transform: none; box-shadow: none; }

    .input-hint {
        text-align: center;
        font-size: 0.7rem;
        color: var(--muted-2);
        margin-top: 0.6rem;
    }

    .clear-btn-wrap { display: flex; align-items: center; gap: 0.75rem; }

    @media (max-width: 768px) {
        .chat-topbar {
            padding: 1rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .chat-topbar-left h1 {
            font-size: 1rem;
        }
        .chat-topbar-left p {
            display: none;
        }
        .clear-btn-wrap {
            width: 100%;
            justify-content: space-between;
        }
        .chat-messages {
            padding: 1rem;
        }
        .bubble {
            max-width: 100%;
        }
        .chat-input-bar {
            padding: 0.75rem 1rem;
            padding-bottom: 2.5rem; /* Extra padding for mobile bottom bar sweeping */
        }
    }
</style>
@endsection

@section('content')
<div class="chat-wrap">

    <div class="chat-topbar">
        <div class="chat-topbar-left">
            <h1>How are you feeling today?</h1>
            <p>Your thoughts are safe here.</p>
        </div>
        <div class="clear-btn-wrap">
            <span class="status-dot">MindEase is here</span>
            @if($messages->count() > 0)
            <form method="POST" action="{{ route('chat.clear') }}">
                @csrf
                <button type="submit" class="btn btn-ghost" style="font-size:0.8rem; padding:0.35rem 0.85rem;" onclick="return confirm('Clear chat?')">
                    Clear
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="chat-messages" id="chat-box">
        @if($messages->isEmpty())
        <div class="empty-state" id="empty-state">
            <h3 style="font-family: 'Merriweather', serif; font-size: 1.35rem; font-weight: 400; color: var(--text); margin-bottom: 0.5rem;">Hello, {{ Auth::user()->name }}</h3>
            <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem;">Share what's on your mind. I'm here to listen without judgment.</p>
            <div class="starters">
                <button class="starter-chip" onclick="useStarter(this)">I've been feeling anxious</button>
                <button class="starter-chip" onclick="useStarter(this)">I need someone to talk to</button>
                <button class="starter-chip" onclick="useStarter(this)">I'm struggling with stress</button>
                <button class="starter-chip" onclick="useStarter(this)">I can't sleep well lately</button>
            </div>
        </div>
        @else
            @foreach($messages as $msg)
            <div class="msg-row {{ $msg->role === 'user' ? 'user' : 'ai' }}">
                <div class="msg-avatar {{ $msg->role === 'user' ? 'user-av' : 'ai-av' }}">
                    {{ $msg->role === 'user' ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'M' }}
                </div>
                <div class="msg-body">
                    <div class="bubble {{ $msg->role === 'user' ? 'user-bubble' : 'ai-bubble' }}">
                        {{ $msg->content }}
                    </div>
                    <span class="msg-time">{{ $msg->created_at->format('h:i A') }}</span>
                </div>
            </div>
            @endforeach
        @endif

        <div class="msg-row ai typing-row" id="typing">
            <div class="msg-avatar ai-av">M</div>
            <div class="typing-bubble">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <div class="chat-input-bar">
        <div class="input-row">
            <div class="input-field-wrap">
                <textarea id="msg-input" rows="1" placeholder="Write how you're feeling..."></textarea>
            </div>
            <button id="send-btn" title="Send message">➤</button>
        </div>
        <p class="input-hint">Press Enter to send · Shift+Enter for new line</p>
    </div>

</div>
@endsection

@section('scripts')
<script>
const chatBox   = document.getElementById('chat-box');
const input     = document.getElementById('msg-input');
const sendBtn   = document.getElementById('send-btn');
const typing    = document.getElementById('typing');
const emptyEl   = document.getElementById('empty-state');
const csrf      = document.querySelector('meta[name="csrf-token"]').content;
const userName  = "{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}";

function scrollDown() { chatBox.scrollTop = chatBox.scrollHeight; }

function addBubble(role, text) {
    if (emptyEl) emptyEl.remove();
    const now = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const isUser = role === 'user';
    const row = document.createElement('div');
    row.className = `msg-row ${isUser ? 'user' : 'ai'}`;
    row.innerHTML = `
        <div class="msg-avatar ${isUser ? 'user-av' : 'ai-av'}">${isUser ? userName : 'M'}</div>
        <div class="msg-body">
            <div class="bubble ${isUser ? 'user-bubble' : 'ai-bubble'}">${text.replace(/\n/g,'<br>')}</div>
            <span class="msg-time">${now}</span>
        </div>`;
    chatBox.insertBefore(row, typing);
    scrollDown();
}

async function send() {
    const msg = input.value.trim();
    if (!msg) return;
    addBubble('user', msg);
    input.value = '';
    input.style.height = 'auto';
    sendBtn.disabled = true;
    typing.classList.add('show');
    scrollDown();

    try {
        const res  = await fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ message: msg }),
        });
        const data = await res.json();
        typing.classList.remove('show');
        addBubble('assistant', data.reply);
    } catch {
        typing.classList.remove('show');
        addBubble('assistant', 'Something went wrong. Please try again. 💙');
    }

    sendBtn.disabled = false;
    input.focus();
}

function useStarter(el) {
    input.value = el.textContent;
    input.focus();
}

sendBtn.addEventListener('click', send);
input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
});
input.addEventListener('input', () => {
    input.style.height = 'auto';
    input.style.height = input.scrollHeight + 'px';
});

scrollDown();
</script>
@endsection