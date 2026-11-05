<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="SARI — Elevated Everyday. A modern multi-vendor e-commerce marketplace."
    >

    <meta
        name="theme-color"
        content="#ffffff"
    >

    <title>
        @yield('title', 'SARI — Elevated Everyday')
    </title>

    {{-- Force the public site to stay in light mode before first paint --}}
    <script>
        (function () {
            const root = document.documentElement;

            root.classList.remove('dark', 'dark-mode');
            root.classList.add('light-mode');
            root.setAttribute('data-theme', 'light');
            root.style.colorScheme = 'light';

            try {
                localStorage.setItem('sari-theme', 'light');
            } catch (error) {
                // Storage can be unavailable; the site still stays light.
            }
        })();
    </script>

    {{-- Preload role-specific assets before render-blocking CSS. --}}
    @stack('preloads')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @unless(request()->routeIs('login'))
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/how-it-works.css') }}">
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ecosystem.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    {{-- Landing overrides: load ONCE and cache-bust automatically when edited --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/landing-compact.css') }}?v={{ file_exists(public_path('css/landing-compact.css')) ? filemtime(public_path('css/landing-compact.css')) : time() }}"
    >

    @endunless

    @stack('styles')
</head>

<body>
    @yield('content')

    {{-- Browser-ready; no ES-module imports required --}}
    @unless(request()->routeIs('login'))
    <script src="{{ asset('js/landing.js') }}"></script>

    {{-- Final light-mode safety after landing.js executes --}}
    <script>
        (function () {
            const root = document.documentElement;

            function keepSariLight() {
                root.classList.remove('dark', 'dark-mode');
                root.classList.add('light-mode');
                root.setAttribute('data-theme', 'light');
                root.style.colorScheme = 'light';

                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode');
                    document.body.classList.add('light-mode');
                }

                try {
                    localStorage.setItem('sari-theme', 'light');
                } catch (error) {
                    // Ignore storage failures.
                }
            }

            keepSariLight();
            document.addEventListener('DOMContentLoaded', keepSariLight);
        })();
    </script>

    @endunless

    @stack('scripts')
    {{-- Authentication feedback only; displayed when an auth flash/error is present. --}}
    @include('components.auth.login-feedback-modal')

</body>
</html>
