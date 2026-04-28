<div class="h-full flex flex-col bg-cream font-poppins">
    <!-- Logo -->
    <div class="p-4 text-center border-b border-sienna bg-terracotta">
        <h1 class="text-xl font-pacifico text-white">Inventory</h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
            🥐 Dashboard
        </a>
        
        <a href="{{ route('orders.index') }}" 
           class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
            🍞 Orders
        </a>

        <a href="{{ route('products.index') }}" 
           class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
            🥖 Products
        </a>

        <a href="{{ route('customers.index') }}" 
           class="flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition">
            🧑‍🍳 Customers
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-sienna">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sage hover:bg-terracotta hover:text-cream rounded-md transition">
                🔒 Logout
            </button>
        </form>
    </div>
</div>
