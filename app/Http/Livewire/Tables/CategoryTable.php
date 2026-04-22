<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class CategoryTable extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.category-table', [
            'categories' => Category::where('category_name', 'like', '%' . $this->search . '%')->paginate(5)
        ]);
    }
}
