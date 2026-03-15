<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MindEase — @yield('title', 'Your Safe Space')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg:        #f7f5f2;
            --surface:   #ffffff;
            --border:    #e8e4de;
            --border-2:  #d4cec6;
            --accent:    #2d5a3d;
            --accent-lt: #e8f0eb;
            --text:      #1a1a18;
            --muted:     #8a8780;
            --muted-2:   #b5b2ac;
            --user-bg:   #1a1a18;
            --user-text: #f7f5f2;
            --danger:    #c0392b;
            --shadow:    0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
        }

        [data-theme="dark"] {
            --bg:        #111410;
            --surface:   #1a1f16;
            --border:    #2a3124;
            --border-2:  #3a4434;
            --accent:    #5a9e6f;
            --accent-lt: #1e2d21;
            --text:      #e8e6e0;
            --muted:     #7a8070;
            --muted-2:   #4a5244;
            --user-bg:   #2d5a3d;
            --user-text: #f7f5f2;
            --danger:    #e06c6c;
            --shadow:    0 1px 3px rgba(0,0,0,0.2), 0 4px 16px rgba(0,0,0,0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            transition: background 0.25s, color 0.25s;
        }

        .app-shell {
            display: flex;
            height: 100dvh; /* use dvh for mobile browsers to account for dynamic address bars */
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 210px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 1.75rem 1.25rem;
            transition: background 0.25s, border-color 0.25s;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2.25rem;
            text-decoration: none;
        }

        .brand-dot {
            width: 30px;
            height: 30px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: background 0.25s;
        }

        .brand-name {
            font-family: 'Merriweather', serif;
            font-size: 1.1rem;
            color: var(--text);
            transition: color 0.25s;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--muted);
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item:hover { background: var(--bg); color: var(--text); padding-left: 0.95rem; }
        .nav-item:active { transform: scale(0.97); }

        .nav-item.active {
            background: var(--accent-lt);
            color: var(--accent);
            font-weight: 500;
        }

        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding-top: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            transition: border-color 0.25s;
        }

        .user-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-name { font-size: 0.8rem; color: var(--muted); font-weight: 500; }

        .btn-logout {
            background: none;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 0.28rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.15s;
        }

        .btn-logout:hover { border-color: var(--danger); color: var(--danger); }

        /* DARK MODE TOGGLE */
        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.45rem 0.75rem;
            border-radius: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-toggle:hover { border-color: var(--accent); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .theme-toggle:active { transform: scale(0.95); box-shadow: none; }

        .toggle-label {
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .toggle-switch {
            width: 32px;
            height: 18px;
            background: var(--border-2);
            border-radius: 9px;
            position: relative;
            transition: background 0.25s;
            flex-shrink: 0;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: transform 0.25s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        [data-theme="dark"] .toggle-switch {
            background: var(--accent);
        }

        [data-theme="dark"] .toggle-switch::after {
            transform: translateX(14px);
        }

        /* MAIN */
        .main {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .alert-success {
            margin: 1rem 2rem 0;
            background: var(--accent-lt);
            border: 1px solid #c2d9c9;
            color: var(--accent);
            padding: 0.65rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        /* SHARED */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.2rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            outline: none;
        }
        
        .btn:active { transform: scale(0.96); }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { opacity: 0.95; transform: translateY(-1.5px); box-shadow: 0 4px 12px rgba(45,90,61,0.25); }
        .btn-primary:active { transform: scale(0.96) translateY(0); box-shadow: none; }
        .btn-primary:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .btn-ghost:hover { border-color: var(--border-2); color: var(--text); transform: translateY(-1.5px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-ghost:active { transform: scale(0.96) translateY(0); box-shadow: none; }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: background 0.25s, border-color 0.25s;
        }

        .mobile-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .menu-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .menu-btn:active { transform: scale(0.85); }

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        @media (max-width: 768px) {
            .app-shell {
                flex-direction: column;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                width: 250px;
                height: 100vh;
                z-index: 50;
                transition: left 0.3s ease;
                box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            }
            .sidebar.open {
                left: 0;
            }
            .mobile-header {
                display: flex;
            }
            .sidebar-footer .theme-toggle {
                display: none;
            }
        }

        @yield('styles')
    </style>

    <script>
        // Apply saved theme before render to avoid flash
        (function() {
            const t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    @yield('head')
</head>
<body>

<div class="app-shell">
    <div class="mobile-overlay" id="mobile-overlay" onclick="toggleSidebar()"></div>
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('chat.index') }}" class="brand">
            <span class="brand-name">MindEase</span>
        </a>

        <nav class="nav">
            <a href="{{ route('chat.index') }}" class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                Chat
            </a>
            <a href="{{ route('mood.index') }}" class="nav-item {{ request()->routeIs('mood.*') ? 'active' : '' }}">
                Mood
            </a>
        </nav>

        <div class="sidebar-footer">
            <!-- Dark mode toggle -->
            <div class="theme-toggle" onclick="toggleTheme()" id="theme-btn">
                <span class="toggle-label" id="theme-label">Dark mode</span>
                <div class="toggle-switch"></div>
            </div>

            <div class="user-row">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Out</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="mobile-header">
            <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            <span class="brand-name" style="font-size: 1.1rem; font-family: 'Merriweather', serif;">MindEase</span>
            <div class="theme-toggle" onclick="toggleTheme()" style="padding: 0; background: transparent; border: none; min-width: 32px; justify-content: flex-end;">
                <div class="toggle-switch"></div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert-success">✓ {{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateLabel(next);
}

function updateLabel(theme) {
    const label = document.getElementById('theme-label');
    if (label) label.innerHTML = theme === 'dark' ? 'Light mode' : 'Dark mode';
}

// Set correct label on load
updateLabel(localStorage.getItem('theme') || 'light');

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar.classList.toggle('open');
    
    if (overlay.classList.contains('active')) {
        overlay.classList.remove('active');
        setTimeout(() => overlay.style.display = 'none', 300);
    } else {
        overlay.style.display = 'block';
        setTimeout(() => overlay.classList.add('active'), 10);
    }
}
</script>

@yield('scripts')
</body>
</html>