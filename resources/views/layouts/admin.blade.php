<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Admin - Mima Studio')</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span>Michele Mariani Studio</span>
            </a>

            @auth
                <nav class="admin-nav" aria-label="Admin">
                    <a @class(['active' => request()->routeIs('admin.portfolio.*')]) href="{{ route('admin.portfolio.index') }}">Portfolio</a>
                    <a @class(['active' => request()->routeIs('admin.client-logos.*')]) href="{{ route('admin.client-logos.index') }}">Loghi clienti</a>
                    <a @class(['active' => request()->routeIs('admin.clients.*')]) href="{{ route('admin.clients.index') }}">Lavori clienti</a>
                    <a @class(['active' => request()->routeIs('admin.customers.*')]) href="{{ route('admin.customers.index') }}">Anagrafica clienti</a>
                    <a @class(['active' => request()->routeIs('admin.customer-access-links.*')]) href="{{ route('admin.customer-access-links.index') }}">Link area clienti</a>
                    <a @class(['active' => request()->routeIs('admin.contact-requests.*')]) href="{{ route('admin.contact-requests.index') }}">Richieste</a>
                    <a @class(['active' => request()->routeIs('admin.work-deliveries.*')]) href="{{ route('admin.work-deliveries.index') }}">Consegna lavoro</a>
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

            @if (session('status_error'))
                <div class="admin-alert error">
                    {{ session('status_error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/admin-gallery.js') }}?v={{ filemtime(public_path('js/admin-gallery.js')) }}" defer></script>
    @stack('scripts')
</body>

</html>
