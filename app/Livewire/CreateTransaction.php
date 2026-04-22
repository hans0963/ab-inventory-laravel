<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends Component {
    public $products;
    public $transactions = [];
    public $total = 0;

    public function mount() {
        $this->products = Products::all();
        $this->transactions = [
            ['product_id' => '', 'quantity' => 1, 'price' => 0]
        ];
    }

    public function addItem() {
        $this->transactions[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removeItem($index) {
        unset($this->transactions[$index]);
        $this->transactions = array_values($this->transactions);
        $this->calculateTotal();
    }

    public function updatedTransactions() {
        $this->calculateTotal();
    }

    public function calculateTotal() {
        $this->total = 0;
        foreach ($this->transactions as $key => $item) {
            if ($item['product_id']) {
                $product = Products::find($item['product_id']);
                $this->transactions[$key]['price'] = $product->price * $item['quantity'];
                $this->total += $this->transactions[$key]['price'];
            }
        }
    }

    public function saveTransaction() {
        DB::transaction(function () {
            foreach ($this->transactions as $item) {
                $product = Products::find($item['product_id']);
                if ($product) {
                    $product->stock -= $item['quantity']; // Deduct stock
                    $product->save();
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Transaction completed successfully!');
    }

    public function render() {
        return view('livewire.create-transaction');
    }
}

