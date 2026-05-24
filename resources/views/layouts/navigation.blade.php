<nav x-data="{ open: false }" class="bg-sienna shadow-md border-b border-cream">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <!-- Sidebar Toggle (Mobile Only) -->
                <button @click="openSidebar = !openSidebar" class="lg:hidden mr-4 text-cream hover:text-terracotta transition">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-cream" />
                    </a>
                </div>

                <!-- Welcome Message -->
                <div class="ms-10">
                    <h2 class="text-2xl font-formal font-semibold text-cream">Welcome back, Shop Owner</h2>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @php
                    $notificationSummary = \Illuminate\Support\Facades\Cache::remember(
                        'nav-notifications-' . Auth::id(),
                        now()->addSeconds(20),
                        fn () => [
                            'items' => \App\Models\SystemNotification::where('user_id', Auth::id())->latest()->limit(8)->get(),
                            'unread_count' => \App\Models\SystemNotification::where('user_id', Auth::id())->whereNull('read_at')->count(),
                        ]
                    );
                    $notifications = $notificationSummary['items'];
                    $unreadNotifications = $notificationSummary['unread_count'];
                @endphp

                <x-dropdown align="right" width="w-80">
                    <x-slot name="trigger">
                        <button class="relative flex items-center px-3 py-2 text-sm font-medium text-cream bg-sienna hover:bg-terracotta focus:outline-none rounded-md border border-cream border-opacity-20">
                            <span>Notifications</span>
                            @if($unreadNotifications > 0)
                                <span class="ml-2 rounded-full bg-cream px-2 py-0.5 text-[10px] font-black text-sienna">{{ $unreadNotifications }}</span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="w-80 max-h-96 overflow-y-auto bg-white">
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                <p class="text-xs font-black uppercase tracking-widest text-sienna">Notifications</p>
                                @if($unreadNotifications > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-widest text-sage hover:text-sienna">Mark all read</button>
                                    </form>
                                @endif
                            </div>

                            @forelse($notifications as $notification)
                                <a href="{{ route('notifications.read', $notification) }}" class="block border-b border-gray-100 px-4 py-3 hover:bg-cream hover:bg-opacity-40">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-sienna">{{ $notification->title }}</p>
                                            <p class="mt-1 text-xs text-sage">{{ \Illuminate\Support\Str::limit($notification->message, 110) }}</p>
                                            <p class="mt-1 text-[10px] uppercase tracking-widest text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(! $notification->read_at)
                                            <span class="mt-1 h-2 w-2 rounded-full bg-terracotta"></span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm italic text-sage">No notifications yet.</div>
                            @endforelse
                        </div>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center px-4 py-2 text-sm font-medium text-cream bg-terracotta hover:bg-terracotta-dark focus:outline-none rounded-md">
                            <span>{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inventory.status')" :active="request()->routeIs('inventory.status')">
                {{ __('Inventory') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                {{ __('Orders') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.index')">
                {{ __('Customers') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
