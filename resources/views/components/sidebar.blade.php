<div class="h-full flex flex-col bg-cream font-inter">
    <!-- Logo -->
    <div class="p-4 text-center border-b border-sienna bg-terracotta">
        <h1 class="text-xl font-formal font-semibold text-white">{{ ucfirst(auth()->user()->role) }}</h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Dashboard (All Roles) -->
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            🏠 Dashboard
        </x-sidebar-link>
        
        <!-- Inventory Management -->
        @can('view-products')
            <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                📦 Products
            </x-sidebar-link>
        @endcan

        @can('view-categories')
            <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                📂 Categories
            </x-sidebar-link>
        @endcan

        <!-- Sales & Customers -->
        @can('view-sales-orders')
            <x-sidebar-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                💰 Sales
            </x-sidebar-link>
        @endcan

        @can('view-customers')
            <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                👥 Customers
            </x-sidebar-link>
        @endcan

        <!-- Production & Stock -->
        @can('view-production-in')
            <x-sidebar-link :href="route('production-in.index')" :active="request()->routeIs('production-in.*')">
                ➡️ Production In
            </x-sidebar-link>
        @endcan

        @can('view-production-out')
            <x-sidebar-link :href="route('production-out.index')" :active="request()->routeIs('production-out.*')">
                ⬅️ Production Out
            </x-sidebar-link>
        @endcan

        @can('view-inventory')
            <x-sidebar-link :href="route('stock-withdrawal.index')" :active="request()->routeIs('stock-withdrawal.*')">
                📤 Stock Withdrawal
            </x-sidebar-link>
        @endcan

        <!-- Purchasing & Suppliers -->
        @can('view-purchases')
            <x-sidebar-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                🛍️ Purchases
            </x-sidebar-link>
            <x-sidebar-link :href="route('inventory-receiving.index')" :active="request()->routeIs('inventory-receiving.*')">
                📥 Receiving
            </x-sidebar-link>
        @endcan

        @can('view-suppliers')
            <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                🚚 Suppliers
            </x-sidebar-link>
        @endcan

        <!-- User & System Management -->
        @can('view-employees')
            <x-sidebar-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                👔 Employees
            </x-sidebar-link>
        @endcan

        @can('admin')
            <x-sidebar-link :href="route('discounts.index')" :active="request()->routeIs('discounts.*')">
                🏷️ Discount Types
            </x-sidebar-link>
        @endcan

        <!-- Reports Section -->
        <div class="pt-4 pb-1">
            <p class="text-[10px] font-black text-sage uppercase tracking-widest px-4 mb-2">Reports</p>
            
            @can('view-manager-sales-report')
                <x-sidebar-link :href="route('inventory.sales')" :active="request()->routeIs('inventory.sales')">
                    📊 Sales Summary
                </x-sidebar-link>
            @endcan

            @can('view-inventory')
                <x-sidebar-link :href="route('reports.inventory')" :active="request()->routeIs('reports.inventory')">
                    📋 Inventory Balance
                </x-sidebar-link>
            @endcan

            @can('view-production-in')
                <x-sidebar-link :href="route('reports.production', ['type' => 'in'])" :active="request()->routeIs('reports.production') && request('type') == 'in'">
                    📈 Production IN
                </x-sidebar-link>
            @endcan

            @can('view-production-out')
                <x-sidebar-link :href="route('reports.production', ['type' => 'out'])" :active="request()->routeIs('reports.production') && request('type') == 'out'">
                    📉 Production OUT
                </x-sidebar-link>
            @endcan

            @can('view-reports')
                <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    📉 Business Intelligence
                </x-sidebar-link>
            @endcan
        </div>
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-sienna">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sage hover:bg-terracotta hover:text-cream rounded-md transition text-sm">
                🔒 Logout
            </button>
        </form>
    </div>
</div>
