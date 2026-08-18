<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

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
        content="#0A0A0A"
    >

    <title>
        @yield('title', 'SARI — Elevated Everyday')
    </title>


    {{-- =====================================================
         GOOGLE FONT
    ====================================================== --}}

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


    {{-- =====================================================
         ORIGINAL SARI CSS
         DO NOT REPLACE THESE WITH TAILWIND
    ====================================================== --}}

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/about.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/how-it-works.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/categories.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/ecosystem.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/footer.css') }}"
    >

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    @stack('styles')

</head>


<body>

    @yield('content')


    {{-- =====================================================
         ORIGINAL SARI JAVASCRIPT
    ====================================================== --}}

    <script
        src="{{ asset('js/app.js') }}"
    ></script>


    @stack('scripts')

</body>

</html>