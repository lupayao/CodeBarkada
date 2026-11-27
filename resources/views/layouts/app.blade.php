<!DOCTYPE html>
{{-- data-bs-theme="dark": Bootstrap's dark mode, re-colored to the Balangay theme in school.css --}}
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Each page fills in its own title with @section('title', '...') --}}
    <title>@yield('title', 'Home') · CodeBarkada</title>
    {{-- Bootstrap first, then our own CSS so ours wins --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="top sticky-top">
        <nav class="navbar navbar-expand-md">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <x-boat-bot size="40" />CodeBarkada
                </a>

                @auth
                    {{-- On phones the menu folds into this button (Bootstrap's JavaScript opens it) --}}
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                            aria-controls="mainNav" aria-expanded="false" aria-label="Open menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="mainNav">
                        {{-- The adviser's menu. "active" highlights the page you're on. --}}
                        @if (auth()->user()->isAdviser())
                            <ul class="navbar-nav me-auto gap-1" aria-label="Adviser menu">
                                <li class="nav-item">
                                    <a href="{{ route('adviser.home') }}" @class(['nav-link', 'active' => request()->routeIs('adviser.home')])
                                       @if (request()->routeIs('adviser.home')) aria-current="page" @endif>Home</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('adviser.members.index') }}" @class(['nav-link', 'active' => request()->routeIs('adviser.members.*')])
                                       @if (request()->routeIs('adviser.members.*')) aria-current="page" @endif>Members</a>
                                </li>
                            </ul>
                        @endif

                        {{-- Your name and the Sign out button --}}
                        <div class="d-flex align-items-center gap-2 ms-auto py-2 py-md-0">
                            <x-avatar :user="auth()->user()" size="32" />
                            <span class="small text-body-secondary">{{ auth()->user()->displayName() }} · {{ ucfirst(auth()->user()->role) }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button class="btn btn-outline-light btn-sm" type="submit">Sign out</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    {{-- A page can add classes to <main> with @section('main_class', '...') --}}
    <main class="container py-4 flex-grow-1 @yield('main_class')">
        {{-- A one-time message, e.g. "Password changed." --}}
        @if (session('status'))
            <div class="alert alert-primary" role="status">{{ session('status') }}</div>
        @endif

        {{-- Each page puts its content here with @section('content') --}}
        @yield('content')
    </main>

    <footer class="container pb-4 small text-body-secondary">
        CodeBarkada · PSHS Caraga Region Campus · ALP S.Y. 2026–2027 · Meetings every Wednesday, Computer Laboratory 1
    </footer>

    {{-- Bootstrap's JavaScript (menu on phones, and later pop-up dialogs) --}}
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
</body>
</html>
