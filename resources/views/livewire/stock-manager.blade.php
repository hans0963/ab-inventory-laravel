<div>
    <table>
        <tr>
            <th>Product</th>
            <th>Stock</th>
            <th>Update</th>
        </tr>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <button wire:click="updateStock({{ $product->id }}, 5)">+5</button>
                    <button wire:click="updateStock({{ $product->id }}, -5)">-5</button>
                </td>
            </tr>
        @endforeach
    </table>
</div>
