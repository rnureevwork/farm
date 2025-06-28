@php
    $config = [
        'appName' => config('app.name'),
        'locale' => $locale = app()->getLocale(),
        'locales' => config('app.locales'),
    ];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Meteo El-Agro</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script>
        window.config = @json($config);
    </script>

    <!-- Scripts -->
    @if(app()->environment('production'))
        <!-- Production: Load built assets -->
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        @endphp

        @if(isset($manifest['resources/sass/app.scss']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/sass/app.scss']['file']) }}">
        @endif

        @if(isset($manifest['resources/js/app.js']))
            <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
        @endif
    @else
        <!-- Development: Use Vite -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
