<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex space-x-4 ml-auto">
                <a href="{{ route('orders.create') }}" 
                   class="bg-terracotta hover:opacity-90 text-cream font-poppins px-6 py-2 rounded-lg shadow-md transition">
                    🛍️ Order
                </a>
                <a href="{{ route('purchases.create') }}" 
                   class="bg-sage hover:opacity-90 text-cream font-poppins px-6 py-2 rounded-lg shadow-md transition">
                    📥 Purchase
                </a>
            </div>
        </div>
    </x-slot>    
    
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <x-card 
                title="Sales Report" 
                description="View the latest sales trends."
                link="{{ route('inventory.sales') }}" 
                color="terracotta" />

            <x-card 
                title="Inventory Status" 
                description="Check stock levels and inventory."
                link="{{ route('inventory.status') }}" 
                color="sage" />

            <x-card 
                title="Employee Management" 
                description="Manage employee records."
                link="{{ route('employees.index') }}" 
                color="cream" />

            <x-card 
                title="Financial Overview" 
                description="Track revenue and expenses."
                link="{{ route('financial.overview') }}" 
                color="sienna" />
        </div>

        <!-- Low Stock Alerts -->
        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-1">
            @if($lowStockAlerts->isNotEmpty())
                <div class="bg-terracotta bg-opacity-20 border-l-4 border-terracotta text-sienna p-4 rounded-lg shadow-lg">
                    <h2 class="text-lg font-pacifico mb-4 text-sienna">⚠️ Low Stock Alerts</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-sienna rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-sienna text-cream">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Current Stock</th>
                                    <th class="px-4 py-2 text-left">Stock Alert Threshold</th>
                                    <th class="px-4 py-2 text-left">Stock Deficit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-cream divide-y divide-sienna">
                                @foreach($lowStockAlerts as $alert)
                                    <tr class="hover:bg-sage hover:bg-opacity-20 transition">
                                        <td class="px-4 py-3 font-semibold text-sienna">{{ $alert->product_name }}</td>
                                        <td class="px-4 py-3 text-sienna">{{ $alert->category_name }}</td>
                                        <td class="px-4 py-3 font-semibold text-terracotta">{{ $alert->current_stock }}</td>
                                        <td class="px-4 py-3 font-semibold text-sage">{{ $alert->stock_alert_threshold }}</td>
                                        <td class="px-4 py-3 font-semibold text-sienna">{{ $alert->stock_deficit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Empty State with Illustration -->
                <div class="flex flex-col items-center justify-center bg-cream border border-sienna rounded-lg p-8 shadow-md">
                    <img src="/images/empty-bread.png" alt="Empty state bread illustration" class="w-32 h-32 mb-4">
                    <p class="font-pacifico text-sienna text-lg">All stocked up — no alerts today!</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
