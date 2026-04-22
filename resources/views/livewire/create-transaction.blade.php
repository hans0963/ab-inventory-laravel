<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create a Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form wire:submit.prevent="saveTransaction">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $item)
                                    <tr>
                                        <td>
                                            <select wire:model="transactions.{{ $index }}.product_id"
                                                    wire:change="updatedTransactions"
                                                    class="border rounded p-2">
                                                <option value="">-- Select Product --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} (₱{{ $product->price }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" wire:model="transactions.{{ $index }}.quantity"
                                                   wire:change="updatedTransactions"
                                                   class="border rounded p-2 w-20"
                                                   min="1">
                                        </td>
                                        <td>₱{{ number_format($transactions[$index]['price'] ?? 0, 2) }}</td>
                                        <td>
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="bg-red-500 text-white px-2 py-1 rounded">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="flex justify-between items-center mt-4">
                            <button type="button" wire:click="addItem"
                                    class="bg-blue-500 text-white px-4 py-2 rounded">
                                + Add Product
                            </button>

                            <h2 class="text-xl font-bold">Total: ₱{{ number_format($total, 2) }}</h2>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded">
                                Complete Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

