<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SendBridge SMS Gateway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #080f1a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #e2e8f0;
        }

        /* Subtle animated gradient background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 40%, #0d2557 0%, transparent 60%),
                radial-gradient(ellipse 50% 60% at 80% 70%, #1a0a33 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; color: #fff;
            box-shadow: 0 0 32px rgba(37,99,235,.35), 0 0 60px rgba(124,58,237,.15);
            margin-bottom: 14px;
        }
        .logo-area h1 {
            font-size: 24px; font-weight: 800;
            color: #f1f5f9; letter-spacing: -.5px;
        }
        .logo-area p {
            font-size: 13px; color: #475569; margin-top: 4px;
        }

        .login-card {
            background: #0d1526;
            border: 1px solid #1e2d45;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 24px 60px rgba(0,0,0,.4);
        }

        .error-box {
            background: #3f0f0f99;
            border: 1px solid #dc262655;
            color: #fca5a5;
            font-size: 13px;
            padding: 11px 14px;
            border-radius: 9px;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 12px; font-weight: 600;
            color: #64748b; text-transform: uppercase; letter-spacing: .06em;
            margin-bottom: 6px;
        }
        .form-group .input-wrap {
            position: relative;
        }
        .form-group .input-wrap .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #475569; font-size: 13px;
        }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            background: #0a1525;
            border: 1px solid #1e2d45;
            border-radius: 9px;
            padding: 11px 12px 11px 36px;
            font-size: 14px;
            color: #f1f5f9;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-group input::placeholder { color: #334155; }
        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #2563eb22;
        }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 22px;
        }
        .remember-row input[type="checkbox"] {
            width: 15px; height: 15px; accent-color: #2563eb; cursor: pointer;
        }
        .remember-row label {
            font-size: 13px; color: #64748b; cursor: pointer;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 14px; font-weight: 600;
            padding: 13px;
            border: none; border-radius: 10px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .15s;
            box-shadow: 0 4px 20px rgba(37,99,235,.3);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            box-shadow: 0 6px 28px rgba(37,99,235,.45);
            transform: translateY(-1px);
        }

        .footer-note {
            text-align: center;
            font-size: 11px; color: #1e2d45;
            margin-top: 20px;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #080f1a; }
        ::-webkit-scrollbar-thumb { background: #1e2d45; border-radius: 10px; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="logo-area">
        <div class="logo-icon"><i class="fa-solid fa-tower-broadcast"></i></div>
        <h1>SendBridge</h1>
        <p>SMS Gateway Admin Panel</p>
    </div>

    <div class="login-card">
        @if($errors->any())
        <div class="error-box">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="admin@sendbridge.local" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Keep me signed in</label>
            </div>
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
        </form>
    </div>

    <div class="footer-note">SendBridge SMS Gateway &copy; {{ date('Y') }}</div>
</div>
</body>
</html>
