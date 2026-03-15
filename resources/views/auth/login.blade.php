<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindEase — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background:
                linear-gradient(to bottom, rgba(10,20,12,0.5) 0%, rgba(10,20,12,0.3) 40%, rgba(10,20,12,0.7) 100%),
                url('https://images.unsplash.com/photo-1501854140801-50d01698950b?w=1800&q=80') center/cover no-repeat fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            color: #fff;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 3rem;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), transparent);
        }

        .nav-brand {
            display: flex; align-items: center;
            gap: 0.6rem; text-decoration: none;
        }

        .brand-leaf {
            font-size: 1.1rem;
            filter: drop-shadow(0 0 6px rgba(110,180,120,0.6));
        }

        .brand-text {
            font-family: 'Merriweather', serif;
            font-size: 1.3rem; font-weight: 600;
            color: #fff; letter-spacing: 0.02em;
        }

        .nav-right { display: flex; align-items: center; gap: 1rem; }

        .nav-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none; font-size: 0.82rem;
            letter-spacing: 0.05em; text-transform: uppercase;
            transition: color 0.2s;
        }

        .nav-link:hover { color: #fff; }

        .theme-toggle-nav {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 0.35rem 0.85rem;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            font-size: 0.78rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 0.4rem;
        }

        .theme-toggle-nav:hover { background: rgba(255,255,255,0.2); }

        @media (max-width: 640px) {
            nav { padding: 1rem 1.5rem; }
        }

        /* CARD */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 24px;
            padding: 2rem 1.5rem;
            box-shadow:
                0 20px 60px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.2);
            margin-top: 2rem;
            animation: fadeUp 0.6s ease both;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .card-header { text-align: center; margin-bottom: 1.5rem; }

        .card-icon {
            width: 44px; height: 44px;
            background: rgba(45,90,61,0.5);
            border: 1px solid rgba(110,180,120,0.4);
            border-radius: 14px;
            display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem;
            margin: 0 auto 0.75rem;
            box-shadow: 0 4px 16px rgba(45,90,61,0.3);
        }

        .card-title {
            font-family: 'Merriweather', serif;
            font-size: 1.6rem; font-weight: 400;
            color: #fff; margin-bottom: 0.25rem;
        }

        .card-sub {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.55);
            font-weight: 300; line-height: 1.6;
        }

        /* FIELDS */
        .field { margin-bottom: 0.75rem; }

        .field label {
            display: block;
            font-size: 0.72rem; font-weight: 500;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .field input {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem; color: #fff;
            outline: none; transition: all 0.2s;
            backdrop-filter: blur(8px);
        }

        .field input::placeholder { color: rgba(255,255,255,0.3); }

        .field input:focus {
            border-color: rgba(110,180,120,0.6);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 0 0 3px rgba(110,180,120,0.15);
        }

        .input-error {
            font-size: 0.73rem; color: #ffb3a7;
            margin-top: 0.3rem;
        }

        /* ERROR BANNER */
        .error-banner {
            background: rgba(180,60,50,0.25);
            border: 1px solid rgba(255,100,80,0.3);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-size: 0.82rem; color: #ffb3a7;
            margin-bottom: 1.25rem; text-align: center;
        }

        /* REMEMBER + FORGOT ROW */
        .field-row {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem; margin-top: 0.25rem;
        }

        .remember {
            display: flex; align-items: center;
            gap: 0.5rem; cursor: pointer;
            font-size: 0.8rem; color: rgba(255,255,255,0.55);
        }

        .remember input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #2d5a3d; cursor: pointer;
        }

        .forgot {
            font-size: 0.8rem; color: #a8d5b0;
            text-decoration: none; transition: color 0.15s;
        }

        .forgot:hover { color: #c8e8cc; }

        /* SUBMIT */
        .btn-submit {
            width: 100%; background: #2d5a3d;
            color: #fff; border: none; border-radius: 30px;
            padding: 0.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem; font-weight: 500;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1rem;
            letter-spacing: 0.03em;
            box-shadow: 0 6px 20px rgba(45,90,61,0.5);
        }

        .btn-submit:hover {
            background: #3a7050;
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(45,90,61,0.5);
        }
        
        .btn-submit:active { transform: scale(0.96) translateY(0); box-shadow: 0 4px 12px rgba(45,90,61,0.3); }

        /* DIVIDER */
        .divider {
            display: flex; align-items: center;
            gap: 0.75rem; margin-bottom: 1rem;
        }

        .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.15); }
        .divider-text { font-size: 0.72rem; color: rgba(255,255,255,0.35); }

        /* REGISTER LINK */
        .register-link {
            text-align: center;
            font-size: 0.84rem;
            color: rgba(255,255,255,0.55);
        }

        .register-link a {
            color: #a8d5b0; text-decoration: none;
            font-weight: 500; transition: color 0.15s;
        }

        .register-link a:hover { color: #c8e8cc; }

        /* TERMS */
        .terms {
            text-align: center;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.3);
            margin-top: 1.25rem; line-height: 1.6;
        }

        .terms a { color: rgba(255,255,255,0.45); text-decoration: underline; }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="{{ route('home') }}" class="nav-brand">
        <span class="brand-text">MindEase</span>
    </a>
    <div class="nav-right">
        <a href="{{ route('register') }}" class="nav-link">Register</a>
    </div>
</nav>

<!-- LOGIN CARD -->
<div class="login-card">
    <div class="card-header">
        <h1 class="card-title">Welcome back</h1>
        <p class="card-sub">Sign in to continue your journey.</p>
    </div>

    @if($errors->any())
    <div class="error-banner">{{ $errors->first() }}</div>
    @endif

    @if(session('status'))
    <div class="error-banner" style="background:rgba(45,90,61,0.25);border-color:rgba(110,180,120,0.3);color:#a8d5b0;">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required autofocus autocomplete="username">
            @error('email')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                placeholder="••••••••"
                required autocomplete="current-password">
            @error('password')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div class="field-row">
            <label class="remember">
                <input type="checkbox" name="remember"> Remember me
            </label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">Sign In</button>

        <a href="{{ route('google.login') }}" class="btn-submit" style="
            background: rgba(255,255,255,0.1); 
            border: 1px solid rgba(255,255,255,0.2); 
            color: #fff; 
            margin-top: -0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: none;
        ">
            <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Continue with Google
        </a>
    </form>

    <div class="divider">
        <div class="divider-line"></div>
        <span class="divider-text">or</span>
        <div class="divider-line"></div>
    </div>

    <p class="register-link">Don't have an account? <a href="{{ route('register') }}">Create one free</a></p>

    <p class="terms">
        By signing in you agree to our
        <a href="#">Privacy Policy</a> and <a href="#">Terms of Service</a>.
    </p>
</div>

<script>
const saved = localStorage.getItem('theme') || 'light';
updateThemeBtn(saved);

function toggleTheme() {
    const curr = document.documentElement.getAttribute('data-theme') || 'light';
    const next = curr === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeBtn(next);
}

function updateThemeBtn(t) {
    const btn = document.getElementById('theme-btn');
    if (btn) btn.innerHTML = t === 'dark' ? '☀️ Light' : '🌙 Dark';
}
</script>
</body>
</html>