<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Each page fills in its own title with @section('title', '...') --}}
    <title>@yield('title', 'Home') · CodeBarkada</title>
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
</head>
<body>
    <header class="top">
        <div class="top-in">
            <a class="brand" href="{{ route('home') }}"><span class="glyph">&lt;/&gt;</span>CodeBarkada</a>

            {{-- Only signed-in users see their name and the Sign out button. --}}
            @auth
                <div class="row top-me">
                    <span class="muted small">{{ auth()->user()->displayName() }} · {{ ucfirst(auth()->user()->role) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn" type="submit">Sign out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main>
        <section class="view">
            {{-- A one-time message, e.g. "Password changed." --}}
            @if (session('status'))
                <p class="banner" role="status">{{ session('status') }}</p>
            @endif

            {{-- Each page puts its content here with @section('content') --}}
            @yield('content')
        </section>
    </main>

    <footer>CodeBarkada · PSHS Caraga Region Campus · ALP S.Y. 2026–2027 · Meetings every Wednesday, Computer Laboratory 1</footer>
</body>
</html>
