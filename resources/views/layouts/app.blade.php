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

        <script>
            document.addEventListener('htmx:configRequest', (event) => {
                event.detail.headers['X-CSRF-Token'] = '{{ csrf_token() }}';
            });
        </script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600&family=Pacifico&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-inter antialiased bg-cream text-sienna">
        <div class="min-h-screen flex" x-data="{ openSidebar: false }">
            <!-- Persistent Sidebar for Desktop -->
            <aside class="hidden lg:flex lg:flex-shrink-0 lg:w-64 border-r border-sienna h-screen sticky top-0 z-40 bg-cream">
                <x-sidebar />
            </aside>

            <!-- Mobile Sidebar Drawer -->
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
                 class="fixed left-0 top-0 h-screen w-64 bg-cream shadow-lg z-50 border-r border-sienna lg:hidden">
                <x-sidebar />
            </div>

            <!-- Mobile Overlay -->
            <div x-show="openSidebar" 
                 x-cloak 
                 @click="openSidebar = false"
                 class="fixed inset-0 bg-black bg-opacity-40 z-40 lg:hidden">
            </div>

            <div class="flex-1 flex flex-col min-w-0">
                @include('layouts.navigation')

                <!-- Main Content -->
                <main class="flex-1 pt-8 pb-12 overflow-y-auto">
                    @if(isset($header) || View::hasSection('header'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                            @if (session('error'))
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                    <span class="block sm:inline">{{ session('success') }}</span>
                                </div>
                            @endif
                            
                            @if(isset($header))
                                {{ $header }}
                            @else
                                @yield('header')
                            @endif
                        </div>
                    @endif
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
