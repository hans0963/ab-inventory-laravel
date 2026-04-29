@props(['alerts'])

@if($alerts->isNotEmpty())
<div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-red-500">
    <h2 class="text-lg font-semibold mb-4 text-red-800 flex items-center">
        <span class="mr-2">⚠️</span> Low Stock Alerts
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-red-500 to-red-700 text-white">
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Category</th>
                    <th class="px-4 py-2 text-left text-center">Current Stock</th>
                    <th class="px-4 py-2 text-left text-center">Threshold</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($alerts as $alert)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="px-4 py-3 font-semibold">{{ $alert->product_name }}</td>
                        <td class="px-4 py-3">{{ $alert->category_name }}</td>
                        <td class="px-4 py-3 font-semibold text-red-600 text-center">{{ $alert->current_stock }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-600 text-center">{{ $alert->stock_alert_threshold }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
