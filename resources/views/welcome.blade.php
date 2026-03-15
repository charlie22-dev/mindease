<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MindEase — Find Your Calm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(
                    to bottom,
                    rgba(10,20,15,0.45) 0%,
                    rgba(10,20,15,0.25) 40%,
                    rgba(10,20,15,0.65) 100%
                ),
                url('https://images.unsplash.com/photo-1501854140801-50d01698950b?w=1800&q=80') center/cover no-repeat;
        }

        /* subtle grain overlay */
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        /* ── NAV ── */
        nav {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 3rem;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), transparent);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .brand-leaf {
            font-size: 1.2rem;
            filter: drop-shadow(0 0 6px rgba(110,180,120,0.6));
        }

        .brand-text {
            font-family: 'Merriweather', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 400;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .nav-link:hover { color: #fff; }

        .nav-cta {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 0.5rem 1.25rem;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-cta:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
        }

        @media (max-width: 640px) {
            nav { padding: 1.25rem 1.5rem; }
            .nav-links { gap: 1rem; }
            .nav-link { display: none; }
        }

        /* ── HERO CONTENT ── */
        .hero-body {
            position: relative;
            z-index: 5;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 1.5rem 5rem;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 0.35rem 0.9rem;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            animation: fadeDown 0.8s ease both;
        }

        .hero-tag-dot {
            width: 6px; height: 6px;
            background: #6eb478;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

        .hero-title {
            font-family: 'Merriweather', serif;
            font-size: clamp(3rem, 8vw, 5.5rem);
            font-weight: 300;
            line-height: 1.1;
            letter-spacing: -0.01em;
            margin-bottom: 1.25rem;
            animation: fadeDown 0.8s 0.1s ease both;
        }

        .hero-title em {
            font-style: italic;
            color: #a8d5b0;
        }

        .hero-sub {
            font-size: 1rem;
            color: rgba(255,255,255,0.72);
            max-width: 420px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            font-weight: 300;
            animation: fadeDown 0.8s 0.2s ease both;
        }

        .hero-btns {
            display: flex;
            gap: 0.85rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeDown 0.8s 0.3s ease both;
        }

        .btn-hero-primary {
            background: #2d5a3d;
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-hero-primary:hover {
            background: #3a7050;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(45,90,61,0.4);
        }

        .btn-hero-ghost {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            border: 1px solid rgba(255,255,255,0.25);
            transition: all 0.2s;
        }

        .btn-hero-ghost:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        @keyframes fadeDown {
            from { opacity:0; transform:translateY(-12px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── SCROLL INDICATOR ── */
        .scroll-hint {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            animation: fadeDown 1s 0.6s ease both;
        }

        .scroll-line {
            width: 1px;
            height: 32px;
            background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
            animation: scrollPulse 1.8s ease-in-out infinite;
        }

        @keyframes scrollPulse {
            0%,100% { transform: scaleY(1); opacity:0.4; }
            50% { transform: scaleY(1.3); opacity:0.8; }
        }

        /* ── CLOUDS ── */
        .clouds-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 2; /* above grain, below hero content */
            opacity: 0.85; /* controls overall cloud intensity */
        }

        .fog-layer {
            position: absolute;
            top: 0; left: 0;
            width: 200%; height: 100%;
            background: transparent url('https://raw.githubusercontent.com/danielstuart14/CSS_FOG_ANIMATION/master/fog1.png') repeat-x;
            background-size: 50% 100%;
            background-position: center bottom;
            animation: fogAnim 70s linear infinite;
            opacity: 0.6;
            mix-blend-mode: screen;
        }

        .fog-layer-2 {
            position: absolute;
            top: -10%; left: 0;
            width: 200%; height: 120%;
            background: transparent url('https://raw.githubusercontent.com/danielstuart14/CSS_FOG_ANIMATION/master/fog2.png') repeat-x;
            background-size: 50% 100%;
            background-position: center 20%;
            animation: fogAnim 120s linear infinite;
            opacity: 0.4;
            mix-blend-mode: screen;
        }

        @keyframes fogAnim {
            0%   { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }

        /* ── FREE CHAT SECTION ── */
        .free-section {
            background: #0d1a10;
            padding: 5rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .section-eyebrow {
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #6eb478;
            margin-bottom: 0.75rem;
        }

        .section-title {
            font-family: 'Merriweather', serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 300;
            text-align: center;
            margin-bottom: 0.75rem;
            color: #f0ede8;
        }

        .section-sub {
            font-size: 0.88rem;
            color: rgba(240,237,232,0.5);
            text-align: center;
            margin-bottom: 2.5rem;
            font-weight: 300;
            max-width: 380px;
            line-height: 1.7;
        }

        /* Chat demo box */
        .chat-demo {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .demo-topbar {
            background: rgba(255,255,255,0.06);
            padding: 0.85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .demo-status {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            color: #6eb478;
        }

        .demo-dot {
            width: 6px; height: 6px;
            background: #6eb478;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .demo-counter {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.06);
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
        }

        .demo-counter span { color: #fff; font-weight: 500; }

        .demo-messages {
            padding: 1rem;
            min-height: 200px;
            max-height: 280px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .demo-messages::-webkit-scrollbar { width: 0; }

        .demo-bubble-row {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
            animation: fadeUp 0.25s ease;
        }

        .demo-bubble-row.user { flex-direction: row-reverse; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(5px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .demo-av {
            width: 24px; height: 24px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
        }

        .demo-av.ai  { background: rgba(110,180,120,0.15); border: 1px solid rgba(110,180,120,0.3); }
        .demo-av.usr { background: #2d5a3d; color: white; font-weight: 600; }

        .demo-bubble {
            padding: 0.55rem 0.8rem;
            border-radius: 12px;
            font-size: 0.82rem;
            line-height: 1.55;
            max-width: 75%;
        }

        .demo-bubble.ai-b {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(240,237,232,0.9);
            border-bottom-left-radius: 3px;
        }

        .demo-bubble.user-b {
            background: #2d5a3d;
            color: #fff;
            border-bottom-right-radius: 3px;
        }

        /* typing dots */
        .typing-dots {
            display: none;
            gap: 4px;
            align-items: center;
            padding: 0.55rem 0.8rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            border-bottom-left-radius: 3px;
        }

        .typing-dots.show { display: flex; }

        .typing-dots span {
            width: 5px; height: 5px;
            background: rgba(240,237,232,0.4);
            border-radius: 50%;
            animation: tb 1.2s infinite;
        }

        .typing-dots span:nth-child(2) { animation-delay:0.15s; }
        .typing-dots span:nth-child(3) { animation-delay:0.3s; }

        @keyframes tb { 0%,80%,100%{transform:translateY(0);opacity:.4;} 40%{transform:translateY(-4px);opacity:1;} }

        .demo-input-row {
            display: flex;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            align-items: flex-end;
        }

        .demo-input {
            flex: 1;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 0.55rem 0.85rem;
            color: #f0ede8;
            font-family: 'Inter', sans-serif;
            font-size: 0.82rem;
            outline: none;
            resize: none;
            max-height: 70px;
            line-height: 1.4;
            transition: border-color 0.15s;
        }

        .demo-input:focus { border-color: rgba(110,180,120,0.4); }
        .demo-input::placeholder { color: rgba(240,237,232,0.3); }

        .demo-send {
            width: 34px; height: 34px;
            background: #2d5a3d;
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .demo-send:hover { background: #3a7050; transform: scale(1.05); }
        .demo-send:disabled { opacity: 0.35; cursor: not-allowed; transform: none; }

        /* Limit reached banner */
        .limit-banner {
            display: none;
            margin: 0 1rem 0.75rem;
            background: rgba(45,90,61,0.2);
            border: 1px solid rgba(110,180,120,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            text-align: center;
        }

        .limit-banner.show { display: block; }

        .limit-banner p {
            font-size: 0.8rem;
            color: rgba(240,237,232,0.7);
            margin-bottom: 0.6rem;
            line-height: 1.5;
        }

        .limit-banner a {
            display: inline-block;
            background: #2d5a3d;
            color: white;
            padding: 0.45rem 1.25rem;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 500;
            transition: all 0.15s;
        }

        .limit-banner a:hover { background: #3a7050; }

        .btn-demo-restart {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.25);
            padding: 0.45rem 1.25rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }

        .btn-demo-restart:hover { background: rgba(255,255,255,0.2); }

        /* ── FEATURES ── */
        .features {
            background: #0a140d;
            padding: 5rem 1.5rem;
        }

        .features-inner {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
        }

        @media (max-width: 640px) {
            .features-grid { grid-template-columns: 1fr; }
        }

        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 1.75rem 1.25rem;
            text-align: left;
            transition: border-color 0.2s, transform 0.2s;
        }

        .feature-card:hover {
            border-color: rgba(110,180,120,0.3);
            transform: translateY(-3px);
        }

        .feature-icon {
            font-size: 1.6rem;
            margin-bottom: 0.85rem;
        }

        .feature-title {
            font-family: 'Merriweather', serif;
            font-size: 1.1rem;
            color: #f0ede8;
            margin-bottom: 0.5rem;
            font-weight: 400;
        }

        .feature-desc {
            font-size: 0.8rem;
            color: rgba(240,237,232,0.45);
            line-height: 1.65;
        }

        /* ── FOOTER ── */
        footer {
            background: #080f09;
            padding: 2rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .footer-brand {
            font-family: 'Merriweather', serif;
            font-size: 1rem;
            color: rgba(255,255,255,0.4);
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-link {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            transition: color 0.15s;
        }

        .footer-link:hover { color: rgba(255,255,255,0.6); }

        @media (max-width: 640px) {
            footer { flex-direction: column; gap: 1rem; padding: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero" id="home">
    <nav>
        <a href="#home" class="nav-brand">
            <span class="brand-text">MindEase</span>
        </a>
        <div class="nav-links">
            <a href="#try" class="nav-link">Try Free</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="{{ route('login') }}?redirect={{ route('home') }}" class="nav-link">Sign In</a>
            <a href="{{ route('register') }}" class="nav-cta">Get Started</a>
        </div>
    </nav>

    <div class="clouds-container">
        <div class="fog-layer"></div>
        <div class="fog-layer-2"></div>
    </div>

    <div class="hero-body">
        <div class="hero-tag">
            <div class="hero-tag-dot"></div>
            AI Mental Health Companion
        </div>
        <h1 class="hero-title">
            A quiet place to<br><em>breathe & heal.</em>
        </h1>
        <p class="hero-sub">
            Share what's on your mind. MindEase listens, supports, and guides you — no judgment, just calm.
        </p>
        <div class="hero-btns">
            <a href="#try" class="btn-hero-primary">Try for Free</a>
            <a href="{{ route('register') }}" class="btn-hero-ghost">Create Account</a>
        </div>
    </div>

    <div class="scroll-hint">
        <span>Explore</span>
        <div class="scroll-line"></div>
    </div>
</section>

<!-- FREE CHAT -->
<section class="free-section" id="try">
    <p class="section-eyebrow">✦ Try it now</p>
    <h2 class="section-title">Talk to MindEase<br>for free</h2>
    <p class="section-sub">No account needed. Start a conversation — you get 10 free messages.</p>

    <div class="chat-demo">
        <div class="demo-topbar">
            <div class="demo-status">
                <div class="demo-dot"></div>
                MindEase is here
            </div>
            <div class="demo-counter">
                <span id="msg-count">15</span> messages left
            </div>
        </div>

        <div class="demo-messages" id="demo-box">
            <div class="demo-bubble-row">
                <div class="demo-av ai">M</div>
                <div class="demo-bubble ai-b">
                    Hello. I'm MindEase. How are you feeling today? You can share anything — I'm here to listen.
                </div>
            </div>

            <div class="demo-bubble-row" id="typing-row" style="display:none">
                <div class="demo-av ai">M</div>
                <div class="typing-dots show">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        <div class="limit-banner" id="limit-banner">
            <p>You've used all 10 free messages. Create a free account to keep chatting with unlimited access.</p>
            <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 0.8rem;">
                <a href="{{ route('register') }}">Create Free Account</a>
                <button onclick="restartDemo()" class="btn-demo-restart">Restart Demo</button>
            </div>
        </div>

        <div class="demo-input-row" id="input-area">
            <textarea class="demo-input" id="demo-input" rows="1" placeholder="Type how you're feeling..."></textarea>
            <button class="demo-send" id="demo-send">➤</button>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
    <div class="features-inner">
        <p class="section-eyebrow">✦ What we offer</p>
        <h2 class="section-title" style="color:#f0ede8;">Everything you need<br>to feel better</h2>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-title">Empathetic Chat</div>
                <div class="feature-desc">Talk to an AI companion that listens, validates your feelings, and responds with warmth and care.</div>
            </div>
            <div class="feature-card">
                <div class="feature-title">Mood Tracking</div>
                <div class="feature-desc">Log your daily mood and visualize patterns over time. Understand yourself better through data.</div>
            </div>
            <div class="feature-card">
                <div class="feature-title">Coping Strategies</div>
                <div class="feature-desc">Get personalized coping techniques — breathing exercises, grounding methods, journaling prompts.</div>
            </div>
        </div>

        <div style="margin-top:3rem;">
            <a href="{{ route('register') }}" class="btn-hero-primary" style="font-size:0.9rem;">
                Start Your Journey — It's Free
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-brand">MindEase</div>
    <div class="footer-links">
        <a href="#" class="footer-link">Privacy Policy</a>
        <a href="#" class="footer-link">Cookie Settings</a>
        <a href="{{ route('login') }}" class="footer-link">Sign In</a>
    </div>
</footer>

<script>
const MAX = 10;
let remaining = parseInt(localStorage.getItem('mindease_free') ?? MAX);
let sessionMessages = [];

const demoBox   = document.getElementById('demo-box');
const demoInput = document.getElementById('demo-input');
const demoSend  = document.getElementById('demo-send');
const typingRow = document.getElementById('typing-row');
const counter   = document.getElementById('msg-count');
const limitBanner = document.getElementById('limit-banner');
const inputArea   = document.getElementById('input-area');

function updateCounter() {
    counter.textContent = Math.max(0, remaining);
    if (remaining <= 0) {
        inputArea.style.display = 'none';
        limitBanner.classList.add('show');
    }
}

function restartDemo() {
    remaining = MAX;
    localStorage.setItem('mindease_free', remaining);
    sessionMessages = [];
    
    // Clear chat bubbles except the first initial greeting message
    const rows = demoBox.querySelectorAll('.demo-bubble-row');
    rows.forEach((row, index) => {
        if (row !== typingRow && index !== 0) {
            row.remove();
        }
    });

    limitBanner.classList.remove('show');
    inputArea.style.display = 'flex';
    updateCounter();
}

function addBubble(role, text) {
    const row = document.createElement('div');
    row.className = `demo-bubble-row ${role === 'user' ? 'user' : ''}`;
    row.innerHTML = `
        <div class="demo-av ${role === 'user' ? 'usr' : 'ai'}">${role === 'user' ? 'U' : 'M'}</div>
        <div class="demo-bubble ${role === 'user' ? 'user-b' : 'ai-b'}">${text.replace(/\n/g,'<br>')}</div>
    `;
    demoBox.insertBefore(row, typingRow);
    demoBox.scrollTop = demoBox.scrollHeight;
}

async function sendDemo() {
    const msg = demoInput.value.trim();
    if (!msg || remaining <= 0) return;

    remaining--;
    localStorage.setItem('mindease_free', remaining);
    updateCounter();

    addBubble('user', msg);
    demoInput.value = '';
    demoInput.style.height = 'auto';
    demoSend.disabled = true;
    typingRow.style.display = 'flex';
    demoBox.scrollTop = demoBox.scrollHeight;

    sessionMessages.push({ role: 'user', content: msg });

    try {
        const res = await fetch('/guest-chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ message: msg, history: sessionMessages })
        });

        const data = await res.json();
        typingRow.style.display = 'none';
        const reply = data.reply || "I'm here for you. Tell me more.";
        sessionMessages.push({ role: 'assistant', content: reply });
        addBubble('assistant', reply);
    } catch {
        typingRow.style.display = 'none';
        addBubble('assistant', 'Something went wrong. Please try again 💙');
    }

    demoSend.disabled = false;
}

demoSend.addEventListener('click', sendDemo);
demoInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendDemo(); }
});
demoInput.addEventListener('input', () => {
    demoInput.style.height = 'auto';
    demoInput.style.height = demoInput.scrollHeight + 'px';
});

updateCounter();
</script>
</body>
</html>