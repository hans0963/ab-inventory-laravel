<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <script src="https://unpkg.com/htmx.org@1.9.10"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600&family=Pacifico&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-inter antialiased bg-cream text-sienna">
        <div class="min-h-screen" x-data="{ openSidebar: false }">
            @include('layouts.navigation')

            <!-- Sidebar -->
            <div x-show="openSidebar" 
                 x-cloak 
                 @click.away="openSidebar = false"
                 @keydown.escape.window="openSidebar = false" 
                 x-transition:enter="transition ease-out duration-200 transform"
                 x-transition:enter-start="-translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-150 transform"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-full opacity-0"
                 class="fixed left-0 top-0 h-screen w-64 bg-cream shadow-lg z-40 border-r border-sienna">
                <x-sidebar />
            </div>

            <!-- Overlay -->
            <div x-show="openSidebar" 
                 x-cloak 
                 @click="openSidebar = false"
                 class="fixed inset-0 bg-black bg-opacity-40 z-30">
            </div>

            <!-- Main Content -->
            <main class="min-h-screen">
                {{ $header ?? '' }}
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
