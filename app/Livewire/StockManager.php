<?php

use App\Models\Products;
use Livewire\Component;
use App\Models\Product;

class StockManager extends Component {
    public $products;

    public function mount() {
        $this->products = Products::all();
    }

    public function render() {
        return view('livewire.stock-manager', ['products' => $this->products]);
    }

    public function updateStock($productId, $quantity) {
        $product = Products::find($productId);
        $product->update(['stock' => $product->stock + $quantity]);
        $this->products = Products::all();
    }
}

