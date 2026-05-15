<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Sales Transactions') }}
            </h2>
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-sienna text-white rounded-md hover:bg-opacity-80">
                + New Sale
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                {{ $message }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <form action="{{ route('sales.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search product..." value="{{ request('search') }}"
                           class="flex-1 px-4 py-2 border-sienna rounded-md focus:ring-terracotta">
                    <button type="submit" class="px-4 py-2 bg-sienna text-white rounded-md hover:bg-opacity-80">
                        Search
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b-2 border-sienna">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Receipt No.</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Date</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Product</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Qty</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Discount</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">VAT</th>
                            <th class="px-6 py-3 text-right font-semibold text-sienna">Total</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Cashier</th>
                            <th class="px-6 py-3 text-center font-semibold text-sienna">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr class="border-b hover:bg-cream hover:bg-opacity-20">
                                <td class="px-6 py-4 font-mono text-xs">{{ $sale->receipt_number }}</td>
                                <td class="px-6 py-4">{{ $sale->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">{{ $sale->product->product_name }}</td>
                                <td class="px-6 py-4 text-center">{{ $sale->sold }}</td>
                                <td class="px-6 py-4">₱{{ number_format($sale->discount_amount, 2) }}</td>
                                <td class="px-6 py-4">₱{{ number_format($sale->vat_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-sienna">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4">{{ $sale->employee->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                    <a href="{{ route('sales.print', $sale->id) }}" class="text-green-500 hover:text-green-700">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No sales found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
