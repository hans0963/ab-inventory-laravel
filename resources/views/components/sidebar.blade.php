@php
    $activeGroup = match (true) {
        request()->routeIs('products.*', 'categories.*', 'raw-materials.*', 'stock-withdrawal.*') => 'inventory',
        request()->routeIs('sales.*', 'customers.*', 'discounts.*', 'cashier-reconciliations.*') => 'sales',
        request()->routeIs('production-management.*') => 'production',
        request()->routeIs('purchases.*', 'inventory-receiving.*', 'suppliers.*') => 'procurement',
        request()->routeIs('employees.*') => 'hr',
        default => null,
    };
@endphp

<div x-data="{ openGroup: @js($activeGroup) }" class="h-full flex flex-col bg-cream font-inter">
    <!-- Logo -->
    <div class="p-4 text-center border-b border-sienna bg-terracotta">
        <h1 class="text-xl font-formal font-semibold text-white">{{ ucfirst(auth()->user()->role) }}</h1>
    </div>

    <!-- Navigation -->
    @php
        $showInventoryProducts = auth()->user()->can('view-products') || auth()->user()->can('view-categories') || auth()->user()->can('view-inventory');
        $showSalesCustomers = auth()->user()->can('view-sales-orders') || auth()->user()->can('view-customers') || auth()->user()->can('view-discounts');
        $showProduction = auth()->user()->can('view-production-in') || auth()->user()->can('view-production-out');
        $showProcurement = auth()->user()->can('view-purchases') || auth()->user()->can('view-suppliers') || auth()->user()->can('view-inventory-receiving');
        $showHR = auth()->user()->can('view-employees');
    @endphp

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Dashboard (All Roles) -->
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-sidebar-link>

        <!-- Inventory & Products -->
        @if($showInventoryProducts)
            <div class="mt-2">
                <button @click="openGroup = 'inventory'" class="w-full flex items-center justify-between px-3 py-2 text-left text-sienna hover:bg-sienna/5 rounded-md">
                    <span class="font-semibold">Inventory & Products</span>
                </button>

                <div x-show="openGroup === 'inventory'" x-cloak class="mt-2 space-y-1 px-2">
                    @can('view-products')
                        <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            Products
                        </x-sidebar-link>
                    @endcan

                    @can('view-categories')
                        <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            Categories
                        </x-sidebar-link>
                    @endcan

                    @can('view-inventory')
                        <x-sidebar-link :href="route('raw-materials.index')" :active="request()->routeIs('raw-materials.*')">
                            Raw Materials
                        </x-sidebar-link>
                    @endcan

                    @can('view-inventory')
                        <x-sidebar-link :href="route('stock-withdrawal.index')" :active="request()->routeIs('stock-withdrawal.*')">
                            Stock Withdrawal
                        </x-sidebar-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Sales & Customers -->
        @if($showSalesCustomers)
            <div class="mt-2">
                <button @click="openGroup = 'sales'" class="w-full flex items-center justify-between px-3 py-2 text-left text-sienna hover:bg-sienna/5 rounded-md">
                    <span class="font-semibold">Sales & Customers</span>
                </button>

                <div x-show="openGroup === 'sales'" x-cloak class="mt-2 space-y-1 px-2">
                    @can('view-sales-orders')
                        <x-sidebar-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                            Sales
                        </x-sidebar-link>
                    @endcan

                    @can('view-cashier-reconciliations')
                        <x-sidebar-link :href="route('cashier-reconciliations.index')" :active="request()->routeIs('cashier-reconciliations.*')">
                            Cashier Reconciliation
                        </x-sidebar-link>
                    @endcan

                    @can('view-customers')
                        <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                            Customers
                        </x-sidebar-link>
                    @endcan

                    @can('view-discounts')
                        <x-sidebar-link :href="route('discounts.index')" :active="request()->routeIs('discounts.*')">
                            Discount Types
                        </x-sidebar-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Production -->
        @if($showProduction)
            <div class="mt-2">
                <button @click="openGroup = 'production'" class="w-full flex items-center justify-between px-3 py-2 text-left text-sienna hover:bg-sienna/5 rounded-md">
                    <span class="font-semibold">Production</span>
                </button>

                <div x-show="openGroup === 'production'" x-cloak class="mt-2 space-y-1 px-2">
                    @can('view-production-in')
                        <x-sidebar-link :href="route('production-management.index', ['tab' => 'in'])" :active="request()->routeIs('production-management.index') && request('tab') === 'in'">
                            Production IN
                        </x-sidebar-link>
                    @endcan

                    @can('view-production-out')
                        <x-sidebar-link :href="route('production-management.index', ['tab' => 'out'])" :active="request()->routeIs('production-management.index') && request('tab') === 'out'">
                            Production OUT
                        </x-sidebar-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Procurement -->
        @if($showProcurement)
            <div class="mt-2">
                <button @click="openGroup = 'procurement'" class="w-full flex items-center justify-between px-3 py-2 text-left text-sienna hover:bg-sienna/5 rounded-md">
                    <span class="font-semibold">Procurement</span>
                </button>

                <div x-show="openGroup === 'procurement'" x-cloak class="mt-2 space-y-1 px-2">
                    @can('view-purchases')
                        <x-sidebar-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                            Purchases
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('inventory-receiving.index')" :active="request()->routeIs('inventory-receiving.*')">
                            Receiving
                        </x-sidebar-link>
                    @endcan

                    @can('view-suppliers')
                        <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                            Suppliers
                        </x-sidebar-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- HR -->
        @if($showHR)
            <div class="mt-2">
                <button @click="openGroup = 'hr'" class="w-full flex items-center justify-between px-3 py-2 text-left text-sienna hover:bg-sienna/5 rounded-md">
                    <span class="font-semibold">HR</span>
                </button>

                <div x-show="openGroup === 'hr'" x-cloak class="mt-2 space-y-1 px-2">
                    @can('view-employees')
                        <x-sidebar-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                            Employees
                        </x-sidebar-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Reports Section -->
        @if(auth()->user()->can('view-manager-sales-report') || auth()->user()->can('view-inventory') || auth()->user()->can('view-production-reports') || auth()->user()->can('view-reports'))
        <div class="pt-4 pb-1">
            <p class="text-[10px] font-black text-sage uppercase tracking-widest px-4 mb-2">Reports</p>
            
            @can('view-manager-sales-report')
                <x-sidebar-link :href="route('inventory.sales')" :active="request()->routeIs('inventory.sales')">
                    Sales Summary
                </x-sidebar-link>
            @endcan

            @can('view-inventory')
                <x-sidebar-link :href="route('reports.inventory')" :active="request()->routeIs('reports.inventory')">
                    Inventory Balance
                </x-sidebar-link>
            @endcan

            @can('view-production-reports')
                <x-sidebar-link :href="route('reports.production')" :active="request()->routeIs('reports.production')">
                    Production Summary
                </x-sidebar-link>
            @endcan

            @can('view-reports')
                <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    Business Intelligence
                </x-sidebar-link>
            @endcan
        </div>
        @endif
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-sienna">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sage hover:bg-terracotta hover:text-cream rounded-md transition text-sm">
                Logout
            </button>
        </form>
    </div>
</div>
