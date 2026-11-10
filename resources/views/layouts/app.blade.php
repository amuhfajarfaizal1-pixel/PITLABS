<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('pitlabs.name')) | {{ config('pitlabs.name') }}</title>
    <meta name="description" content="@yield('description', config('pitlabs.description'))">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('pitlabs.name') }}">
    <meta property="og:title" content="@yield('title', config('pitlabs.name'))">
    <meta property="og:description" content="@yield('description', config('pitlabs.description'))">
    <meta property="og:locale" content="id_ID">
    <meta name="theme-color" content="#0A2540">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/pitlabs.css') }}">

    @stack('head')
</head>
<body class="@yield('body-class')">

    <a class="pit-skip-link" href="#main">Lewati ke konten utama</a>

    @include('partials.navbar')

    <main id="main" tabindex="-1">
        @if (session('status'))
            <div class="container pt-3">
                <x-alert type="success">{{ session('status') }}</x-alert>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.ai-assistant')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
    <script src="{{ asset('assets/js/pitlabs.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
