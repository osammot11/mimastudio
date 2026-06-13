<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Admin - Mima Studio')</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=1.0">
</head>

<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span>Mima Studio</span>
                <small>Admin</small>
            </a>

            @auth
                <nav class="admin-nav" aria-label="Admin">
                    <a @class(['active' => request()->routeIs('admin.portfolio.*')]) href="{{ route('admin.portfolio.index') }}">Portfolio</a>
                    <a @class(['active' => request()->routeIs('admin.clients.*')]) href="{{ route('admin.clients.index') }}">Clienti</a>
                </nav>

                <div class="admin-sidebar-footer">
                    <a href="{{ route('home') }}" target="_blank">Apri sito</a>
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="admin-eyebrow">@yield('eyebrow', 'Admin')</p>
                    <h1>@yield('page-title', 'Pannello')</h1>
                </div>

                <div class="admin-topbar-actions">
                    @yield('actions')
                </div>
            </header>

            @if (session('status'))
                <div class="admin-alert">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>
