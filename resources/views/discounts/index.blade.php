<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Discount Types') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage percentage, fixed amount, and special customer discounts</p>
            </div>
            <a href="{{ route('discounts.create') }}" class="btn-terracotta">
                <span class="mr-2 text-xl">+</span> New Discount
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if ($message = Session::get('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ $message }}</div>
        @endif

        @if ($message = Session::get('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ $message }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach(['Senior Citizen' => '20%', 'PWD' => '20%', 'Bulk Order' => 'Volume', 'Loyalty Discount' => 'Reward'] as $name => $value)
                <div class="bg-white border-l-4 border-sage rounded-lg shadow-rustic p-5">
                    <p class="text-xs font-black text-sage uppercase tracking-widest">{{ $name }}</p>
                    <p class="mt-2 text-2xl font-bold text-sienna">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="card-rustic border-sienna">
            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-4 py-3 text-left">Discount ID</th>
                            <th class="px-4 py-3 text-left">Discount Name</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Value</th>
                            <th class="px-4 py-3 text-left">Applicable To</th>
                            <th class="px-4 py-3 text-left">Valid Date Range</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($discountTypes as $discount)
                            @php
                                $effectiveStatus = $discount->effective_status;
                                $statusClass = match($effectiveStatus) {
                                    'Active' => 'bg-green-100 text-green-800',
                                    'Expired' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $discount->id }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-sienna">{{ $discount->discount_name }}</div>
                                    <div class="text-xs text-sage">{{ $discount->description ?? 'No description' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sienna">{{ $discount->discount_type ?? 'Percentage' }}</td>
                                <td class="px-4 py-4 font-black text-terracotta">{{ $discount->display_value }}</td>
                                <td class="px-4 py-4 text-sage">{{ $discount->applicable_to ?? 'All' }}</td>
                                <td class="px-4 py-4 text-gray-600">
                                    {{ $discount->start_date?->format('M d, Y') ?? 'Anytime' }}
                                    -
                                    {{ $discount->end_date?->format('M d, Y') ?? 'No end' }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $effectiveStatus }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('discounts.edit', $discount) }}" class="text-sage hover:text-sienna font-bold text-xs uppercase tracking-widest">Edit</a>
                                        <form method="POST" action="{{ route('discounts.destroy', $discount) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this discount type?')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-widest">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 italic">No discount types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $discountTypes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
