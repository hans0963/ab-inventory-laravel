<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- Low Stock Alerts Section --}}
                @if($lowStockAlerts->isNotEmpty())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 text-red-800">⚠️ Low Stock Alerts</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gradient-to-r from-red-500 to-red-700 text-white">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Current Stock</th>
                                    <th class="px-4 py-2 text-left">Stock Alert Threshold</th>
                                    <th class="px-4 py-2 text-left">Stock Deficit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lowStockAlerts as $alert)
                                    <tr class="hover:bg-gray-100 transition">
                                        <td class="px-4 py-3 font-semibold">{{ $alert->product_name }}</td>
                                        <td class="px-4 py-3">{{ $alert->category_name }}</td>
                                        <td class="px-4 py-3 font-semibold text-red-600">{{ $alert->current_stock }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-600">{{ $alert->stock_alert_threshold }}</td>
                                        <td class="px-4 py-3 font-semibold text-yellow-600">{{ $alert->stock_deficit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Products Section --}}
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">📦 Product Inventory</h2>
                        <form method="GET" action="{{ route('inventory.status') }}" class="flex gap-4">
                            <select name="product" class="border rounded-lg p-2 w-48">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-600">
                                Filter
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Stock Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-100 transition">
                                        <td class="px-4 py-3">{{ $product->product_name }}</td>
                                        <td class="px-4 py-3">{{ $product->category->category_name }}</td>
                                        <td class="px-4 py-3 font-semibold text-blue-600">{{ $product->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Inventory Movements Section --}}
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">📊 Recent Inventory Movements</h2>
                        {{-- Filters --}}
                        <form method="GET" action="{{ route('inventory.status') }}" class="flex gap-4">
                            <input type="date" name="date" value="{{ request('date') }}" class="border rounded-lg p-2">
                            <select name="product" class="border rounded-lg p-2 w-48">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Filter</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gradient-to-r from-green-500 to-green-700 text-white">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-left">Balance Forwarded</th>
                                    <th class="px-4 py-2 text-left">Pull Out</th>
                                    <th class="px-4 py-2 text-left">New Balance</th>
                                    <th class="px-4 py-2 text-left">Total</th>
                                    <th class="px-4 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inventoryMovements as $movement) 
                                    <tr class="hover:bg-gray-100 transition">
                                        <td class="px-4 py-3">{{ $movement->product->product_name }}</td>
                                        <td class="px-4 py-3">{{ $movement->date }}</td>
                                        <td class="px-4 py-3 font-semibold">
                                            @if($movement->transaction_type === 'STACK_IN')
                                                <span class="text-blue-600">🔵 STACK IN</span>
                                            @elseif($movement->transaction_type === 'SALE')
                                                <span class="text-green-600">🟢 SALE</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $movement->balance_forwarded }}</td>
                                        <td class="px-4 py-3 text-red-500">{{ $movement->pull_out }}</td>
                                        <td class="px-4 py-3 font-semibold text-green-600">{{ $movement->new_luto }}</td>
                                        <td class="px-4 py-3">{{ $movement->total_inventory }}</td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('inventory_movements.destroy', $movement->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-lg shadow-md hover:bg-red-600">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $inventoryMovements->links() }}
                    </div>
                </div>


                {{-- Recent Sales --}}
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">🛒 Recent Sales</h2>
                        <form method="GET" action="{{ route('inventory.status') }}" class="flex gap-4">
                            <input type="date" name="date" value="{{ request('date') }}" class="border rounded-lg p-2 w-48">
                            <select name="product" class="border rounded-lg p-2 w-48">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-purple-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-purple-600">
                                Filter
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gradient-to-r from-purple-500 to-purple-700 text-white">
                                    <th class="px-4 py-2 text-left">Customer</th>
                                    <th class="px-4 py-2 text-left">Sold By</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Total Quantity</th>
                                    <th class="px-4 py-2 text-left">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sales as $sale)
                                    <tr class="hover:bg-gray-100 transition">
                                        <td class="px-4 py-3">{{ $sale->customer_name }}</td>
                                        <td class="px-4 py-3">{{ $sale->sold_by }}</td>
                                        <td class="px-4 py-3">{{ $sale->order_date }}</td>
                                        <td class="px-4 py-3 font-semibold text-red-600">{{ $sale->total_quantity_sold }}</td>
                                        <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($sale->total_sales_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination Links for Recent Sales --}}
                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
