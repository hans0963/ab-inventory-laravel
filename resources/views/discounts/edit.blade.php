<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Edit Discount') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">{{ $discount->discount_name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('discounts.update', $discount) }}" class="space-y-8">
            @csrf
            @method('PUT')

            @include('discounts.partials.form', ['discount' => $discount])

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('discounts.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">Back to Discounts</a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">Update Discount</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
