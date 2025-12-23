<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script>
        window.isAuthenticated = @json(auth()->check());
    </script>
    @auth
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @endauth

    <x-turnstile.scripts />
    @livewireStyles
</head>

<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')
        <main class="pt-16">
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>

</html>
