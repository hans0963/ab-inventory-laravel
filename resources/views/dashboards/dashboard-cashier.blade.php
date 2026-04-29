<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-lora font-semibold text-3xl text-sienna leading-tight">
                {{ __('Cashier Dashboard') }}
            </h2>
        </div>
    </x-slot>    
    
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Today's Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaysSales, 2)" icon="💵" border="terracotta" />
            <x-stat-card title="Transactions Today" :value="$transactionsCount" icon="🧾" border="sage" />
        </div>

        <!-- Low Stock Alerts -->
        <x-alert-table :alerts="$lowStockAlerts" />
    </div>
</x-app-layout>
