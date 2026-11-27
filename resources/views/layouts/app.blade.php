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
        </div>
    </header>

    <main>
        <section class="view">
            {{-- Each page puts its content here with @section('content') --}}
            @yield('content')
        </section>
    </main>

    <footer>CodeBarkada · PSHS Caraga Region Campus · ALP S.Y. 2026–2027 · Meetings every Wednesday, Computer Laboratory 1</footer>
</body>
</html>
