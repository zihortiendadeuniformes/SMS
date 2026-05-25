<!DOCTYPE html>
<html lang="en" style="height:100%;background:#080f1a;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SendBridge') — SMS Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            height: 100%;
            display: flex;
            background: #080f1a;
            color: #e2e8f0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* ── Sidebar ─────────────────────────────── */
        #sidebar {
            width: 240px;
            min-width: 240px;
            background: #0d1526;
            border-right: 1px solid #1e2d45;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
        }

        #sidebar .logo {
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid #1e2d45;
            gap: 10px;
        }
        #sidebar .logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
            box-shadow: 0 0 16px rgba(37,99,235,.4);
        }
        #sidebar .logo span { font-size: 16px; font-weight: 700; color: #f1f5f9; letter-spacing: -.3px; }

        #sidebar nav { flex: 1; overflow-y: auto; padding: 12px 10px; }

        #sidebar nav .nav-section {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 12px 10px 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: all .15s;
            margin-bottom: 2px;
        }
        .sidebar-link:hover { background: #162032; color: #e2e8f0; }
        .sidebar-link.active {
            background: linear-gradient(135deg, #1e3a5f, #1e40af22);
            color: #60a5fa;
            border: 1px solid #1e40af44;
        }
        .sidebar-link .icon {
            width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 7px;
            font-size: 13px;
            background: #0f1e33;
            flex-shrink: 0;
            transition: all .15s;
        }
        .sidebar-link.active .icon { background: #1e3a8a44; color: #60a5fa; }
        .sidebar-link:hover .icon { background: #162032; }

        #sidebar .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid #1e2d45;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #sidebar .sidebar-footer .avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        #sidebar .sidebar-footer .user-name { font-size: 12px; font-weight: 600; color: #cbd5e1; flex: 1; }
        #sidebar .sidebar-footer .logout-btn {
            font-size: 11px; color: #ef4444; background: none; border: none;
            cursor: pointer; padding: 4px 8px; border-radius: 6px;
            transition: background .15s;
        }
        #sidebar .sidebar-footer .logout-btn:hover { background: #3f1212; }

        /* ── Main ────────────────────────────────── */
        #main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        #topbar {
            height: 64px;
            background: #0d1526;
            border-bottom: 1px solid #1e2d45;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
        }
        #topbar h1 { font-size: 16px; font-weight: 700; color: #f1f5f9; letter-spacing: -.2px; }
        #topbar .topbar-right {
            display: flex; align-items: center; gap: 12px;
            font-size: 12px; color: #64748b;
        }
        #topbar .status-dot {
            width: 8px; height: 8px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 8px #22c55e88;
        }

        #main-content { flex: 1; overflow-y: auto; padding: 24px; }

        /* ── Alerts ──────────────────────────────── */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: 10px;
            font-size: 13px; margin-bottom: 16px;
        }
        .alert-success { background: #052e16bb; border: 1px solid #16a34a55; color: #86efac; }
        .alert-error   { background: #3f0f0fbb; border: 1px solid #dc262655; color: #fca5a5; }

        /* ── Cards ───────────────────────────────── */
        .card {
            background: #0d1526;
            border: 1px solid #1e2d45;
            border-radius: 14px;
            padding: 20px;
        }

        /* ── Buttons ─────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            border: none; cursor: pointer; text-decoration: none;
            transition: all .15s; white-space: nowrap;
        }
        .btn-primary  { background: #2563eb; color: #fff; }
        .btn-primary:hover  { background: #1d4ed8; box-shadow: 0 0 16px #2563eb44; }
        .btn-danger   { background: #dc2626; color: #fff; }
        .btn-danger:hover   { background: #b91c1c; }
        .btn-secondary{ background: #1e2d45; color: #94a3b8; border: 1px solid #2d3f55; }
        .btn-secondary:hover{ background: #243347; color: #e2e8f0; }
        .btn-success  { background: #16a34a; color: #fff; }
        .btn-success:hover  { background: #15803d; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 6px; }

        /* ── Form inputs ─────────────────────────── */
        .form-label { display: block; font-size: 12px; font-weight: 500; color: #94a3b8; margin-bottom: 5px; }
        .form-input {
            width: 100%;
            background: #0a1525;
            border: 1px solid #1e2d45;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13px;
            color: #e2e8f0;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px #2563eb22; }
        select.form-input option { background: #0d1526; }

        /* ── Table ───────────────────────────────── */
        .table-wrap { overflow-x: auto; border-radius: 14px; border: 1px solid #1e2d45; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.data-table thead { background: #0f1d30; }
        table.data-table thead th {
            padding: 12px 16px;
            font-size: 11px; font-weight: 600;
            color: #64748b; text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1px solid #1e2d45;
        }
        table.data-table tbody tr { border-top: 1px solid #111e2f; transition: background .1s; }
        table.data-table tbody tr:hover { background: #0f1c2e; }
        table.data-table tbody td { padding: 12px 16px; color: #cbd5e1; }

        /* ── Badges ──────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 600; letter-spacing: .02em;
        }
        .badge-online,   .badge-active, .badge-sent     { background: #052e1666; color: #4ade80; border: 1px solid #16a34a44; }
        .badge-offline                                   { background: #1e293b66; color: #94a3b8; border: 1px solid #33485f44; }
        .badge-disabled, .badge-inactive, .badge-failed,
        .badge-cancelled                                 { background: #3f0f0f66; color: #f87171; border: 1px solid #dc262644; }
        .badge-pending                                   { background: #3f290066; color: #fbbf24; border: 1px solid #d9770644; }
        .badge-reserved, .badge-sending                  { background: #0c1e4066; color: #60a5fa; border: 1px solid #2563eb44; }

        /* ── Pagination ──────────────────────────── */
        nav[role=navigation] span, nav[role=navigation] a {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 6px 12px; font-size: 12px; border-radius: 7px;
            border: 1px solid #1e2d45; background: #0d1526; color: #64748b;
            margin: 0 2px; text-decoration: none; transition: all .15s;
        }
        nav[role=navigation] a:hover { background: #162032; color: #e2e8f0; }
        nav[role=navigation] span[aria-current] { background: #1e3a8a; color: #93c5fd; border-color: #2563eb44; }

        /* ── Scrollbar ───────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #080f1a; }
        ::-webkit-scrollbar-thumb { background: #1e2d45; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #2d3f55; }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div id="sidebar">
    <div class="logo">
        <div class="logo-icon"><i class="fa-solid fa-tower-broadcast"></i></div>
        <span>SendBridge</span>
    </div>

    <nav>
        <div class="nav-section">Overview</div>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
        </a>

        <div class="nav-section">Management</div>
        <a href="{{ route('admin.clients.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-users"></i></span> Clients
        </a>
        <a href="{{ route('admin.devices.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.devices*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-mobile-screen"></i></span> Devices
        </a>
        <a href="{{ route('admin.api_keys.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.api_keys*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-key"></i></span> API Keys
        </a>

        <div class="nav-section">Messaging</div>
        <a href="{{ route('admin.sms.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.sms.index') || request()->routeIs('admin.sms.show') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-comment-sms"></i></span> SMS Messages
        </a>
        <a href="{{ route('admin.sms.compose') }}"
           class="sidebar-link {{ request()->routeIs('admin.sms.compose') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-paper-plane"></i></span> Send SMS
        </a>
        <a href="{{ route('admin.blocked_numbers.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.blocked_numbers*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-ban"></i></span> Blocked Numbers
        </a>

        <div class="nav-section">Connect</div>
        <a href="{{ route('admin.integrations.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.integrations*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-plug-circle-bolt"></i></span> Integrations
        </a>

        <div class="nav-section">System</div>
        <a href="{{ route('admin.logs.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-list-ul"></i></span> Logs
        </a>
        <a href="{{ route('admin.settings.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-gear"></i></span> Settings
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="icon"><i class="fa-solid fa-user-shield"></i></span> Users
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <span class="user-name">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</div>

{{-- Main Wrapper --}}
<div id="main-wrapper">
    <div id="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        <div class="topbar-right">
            <div class="status-dot"></div>
            <span>{{ now()->format('M d, Y · H:i') }}</span>
        </div>
    </div>

    <div id="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-error" style="flex-direction:column;align-items:flex-start;">
                @foreach($errors->all() as $e)
                    <div><i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</div>

</body>
</html>
