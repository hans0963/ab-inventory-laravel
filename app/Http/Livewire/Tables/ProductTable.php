<?php

namespace App\Http\Livewire\Tables;

use Livewire\Component;
use App\Models\Products;

class ProductTable extends Component
{
    public function render()
    {
        $prod = Products::all(); // Fetch all categories

        return view('livewire.tables.product-table', [
            'products' => $prod
        ]);
    }
}
