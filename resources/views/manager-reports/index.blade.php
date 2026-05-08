<x-app-layout>
    <x-slot name="header">
        <div class="mb-4">
            <h1 class="text-3xl font-formal font-bold text-[#5a3e2b]">Inventory & Production Reports</h1>
            <p class="text-gray-500">Comprehensive analytics for operations</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- TOP METRICS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-3xl font-bold text-[#5a3e2b]">{{ number_format($weeklyProduction ?? 3830) }}</p>
                <p class="text-gray-500">Total Production (Week)</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-3xl font-bold text-red-500">{{ $weeklyWaste ?? 93 }} units</p>
                <p class="text-gray-500">Total Waste (Week)</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-3xl font-bold text-orange-500">{{ $wastePercent ?? '2.4%' }}</p>
                <p class="text-gray-500">Waste Percentage</p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-3xl font-bold text-green-600">{{ $lowStockCount ?? 2 }}</p>
                <p class="text-gray-500">Low Stock Items</p>
            </div>

        </div>

        <!-- CHART -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Production vs Waste (Last 7 Days)</h3>

            <!-- Replace with Chart.js / ApexCharts -->
            <canvas id="productionChart" height="100"></canvas>
        </div>

        <!-- WASTE BREAKDOWN -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Waste Breakdown by Reason</h3>

            @php
                $waste = [
                    ['label' => 'Stale', 'value' => 45, 'percent' => 52],
                    ['label' => 'Damaged', 'value' => 23, 'percent' => 27],
                    ['label' => 'Expired', 'value' => 12, 'percent' => 14],
                    ['label' => 'Lost', 'value' => 6, 'percent' => 7],
                ];
            @endphp

            <div class="space-y-4">
                @foreach($waste as $item)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $item['label'] }}</span>
                            <span>{{ $item['value'] }} units ({{ $item['percent'] }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-400 h-2 rounded-full" style="width: {{ $item['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RAW MATERIALS -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Raw Materials Stock Level</h3>

            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 border-b">
                        <th class="text-left py-2">Material</th>
                        <th class="text-left py-2">Stock</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">

                    @foreach($materials ?? [] as $item)
                        <tr>
                            <td class="py-3">{{ $item['name'] }}</td>
                            <td>{{ $item['stock'] }}</td>
                            <td>
                                <span class="px-3 py-1 rounded-full text-xs
                                    @if($item['status']=='Good') bg-green-100 text-green-600
                                    @elseif($item['status']=='Low') bg-yellow-100 text-yellow-600
                                    @else bg-red-100 text-red-600 @endif">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- INSIGHTS -->
        <div class="space-y-4">

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold mb-2">Production Efficiency Insights</h3>

                <div class="bg-gray-100 p-4 rounded-lg">
                    <p class="font-semibold">Production Rate</p>
                    <p class="text-sm text-gray-500">Average: {{ $avgProduction ?? 547 }} units/day</p>
                    <p class="text-sm mt-2 text-gray-600">
                        Consistent production with 15% increase this week
                    </p>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                <p class="font-semibold text-yellow-700">Waste Alert</p>
                <p class="text-sm text-gray-600">2.4% waste rate</p>
                <p class="text-sm mt-1">52% of waste is due to stale products - consider demand adjustment</p>
            </div>

            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                <p class="font-semibold text-red-700">Stock Alert</p>
                <p class="text-sm text-gray-600">2 items need reordering</p>
                <p class="text-sm mt-1">Butter and Yeast are running low - schedule purchase order</p>
            </div>

        </div>

    </div>
</x-app-layout>