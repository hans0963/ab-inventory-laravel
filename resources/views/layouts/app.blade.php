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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
    <div x-data="{ openSidebar: false }">
        <!-- Sidebar Toggle Button -->
        <button @click="openSidebar = !openSidebar" 
            class="p-2 bg-gray-800 text-white rounded-md fixed top-4 left-4 z-50">
            ☰
        </button>

        <!-- Overlay (Click outside to close) -->
        <div x-show="openSidebar" 
             x-cloak 
             @click="openSidebar = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-30">
        </div>

        <!-- Sidebar -->
        <div x-show="openSidebar" 
             x-cloak 
             @keydown.escape.window="openSidebar = false" 
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="-translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="-translate-x-full opacity-0"
             class="fixed left-0 top-0 h-screen w-64 bg-white dark:bg-gray-900 shadow-lg z-40">
            <x-sidebar>
                {{ $slot }}
            </x-sidebar>
        </div>
    </div>

    <div class="min-h-screen">
        {{ $slot }} <!-- Your Page Content -->
    </div>
        </div>
    </body>
</html>
