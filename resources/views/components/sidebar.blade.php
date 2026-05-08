<div class="h-full flex flex-col bg-cream font-inter">
    <!-- Logo -->
    <div class="p-4 text-center border-b border-sienna bg-terracotta">
        <h1 class="text-xl font-formal font-semibold text-white">{{ ucfirst(auth()->user()->role) }}</h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Dashboard (All Roles) -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
            🏠 Dashboard
        </a>
        
        <!-- Cashier Modules Only -->
        @if(auth()->user()->isCashier())
            <a href="{{ route('products.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📦 Products
            </a>
            <a href="{{ route('sales-orders.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                🛒 Sales Orders
            </a>
            <a href="{{ route('customers.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                👥 Customers
            </a>
        @endif

        <!-- Manager Modules Only -->
        @if(auth()->user()->isManager())
            <a href="{{ route('products.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📦 Products
            </a>
            <a href="{{ route('categories.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📂 Categories
            </a>
            <a href="{{ route('orders.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                🛒 Sales Orders
            </a>
            <a href="{{ route('customers.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                👥 Customers
            </a>
            <a href="{{ route('raw-materials.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                🌾 Raw Materials
            </a>
            <a href="{{ route('production-in.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                ➡️ Production In
            </a>
            <a href="{{ route('production-out.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                ⬅️ Production Out
            </a>
            <a href="{{ route('purchases.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                🛍️ Purchases
            </a>
            <a href="{{ route('manager-sales-report.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📊 Sales Report
            </a>
            <a href="{{ route('manager-reports.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📈 Inventory & Production Reports
            </a>
        @endif

        <!-- Admin Modules Only -->
        @if(auth()->user()->isAdmin())
            <a href="{{ route('sales-report.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📊 Sales Report
            </a>
            <a href="{{ route('reports.index') }}" 
               class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
                📈 Reports
            </a>
        @endif
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
