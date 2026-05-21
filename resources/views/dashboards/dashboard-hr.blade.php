<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('HR Dashboard') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage your bakeshop's human resources and personnel</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <a href="{{ route('employees.index') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Total Employees" :value="$totalEmployees" icon="👥" border="sage" />
            </a>
            <div class="block transform hover:scale-105 transition">
                <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaySales, 2)" icon="💰" border="terracotta" />
            </div>
            <div class="block transform hover:scale-105 transition">
                <x-stat-card title="Monthly Sales" :value="'₱' . number_format($monthlySales, 2)" icon="📅" border="sienna" />
            </div>
            <div class="block transform hover:scale-105 transition">
                <x-stat-card title="Yearly Sales" :value="'₱' . number_format($yearlySales, 2)" icon="📈" border="cream" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Employees -->
            <div class="card-rustic border-sage">
                <h2 class="text-xl font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">👤</span> Recent Employees
                </h2>
                <div class="space-y-4">
                    @forelse($recentEmployees as $employee)
                        <div class="flex items-center justify-between p-3 bg-cream bg-opacity-30 rounded-lg">
                            <span class="font-medium text-sienna">{{ $employee->employee_name }}</span>
                            <span class="bg-sage text-white px-2 py-1 rounded text-xs font-bold">{{ $employee->position }}</span>
                        </div>
                    @empty
                        <p class="text-sage italic text-center py-4">No data available</p>
                    @endforelse
                </div>
                <div class="mt-6">
                    <a href="{{ route('employees.index') }}" class="text-sienna hover:underline font-bold text-sm">View All Employees →</a>
                </div>
            </div>

            <!-- Fast Moving Products -->
            <div class="card-rustic border-terracotta">
                <h2 class="text-xl font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🚀</span> Fast Moving Products
                </h2>
                <div class="space-y-4">
                    @forelse($fastMovingProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-cream bg-opacity-30 rounded-lg">
                            <span class="font-medium text-sienna">{{ $product->product_name }}</span>
                            <span class="bg-sage text-white px-2 py-1 rounded text-xs font-bold">{{ $product->total_sold }} sold</span>
                        </div>
                    @empty
                        <p class="text-sage italic text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
