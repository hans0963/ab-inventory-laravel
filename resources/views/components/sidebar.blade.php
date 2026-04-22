<div class="h-full flex flex-col">
    <!-- Logo -->
    <div class="p-4 text-center border-b border-gray-700">
        <h1 class="text-xl font-bold text-white">Inventory</h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('dashboard') }}" 
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V10z"></path>
            </svg>
            Dashboard
        </a>
        
        <a href="{{ route('orders.index') }}" 
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v2a1 1 0 001 1h4a1 1 0 001-1v-2M5 7h14l1 9H4l1-9z"></path>
            </svg>
            Orders
        </a>

        <a href="{{ route('products.index') }}" 
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v6H3zM3 9h18v12H3z"></path>
            </svg>
            Products
        </a>

        <a href="{{ route('customers.index') }}" 
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20h14l1-9H4l1 9zM5 7h14V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2z"></path>
            </svg>
            Customers
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
