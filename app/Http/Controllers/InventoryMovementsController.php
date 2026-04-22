<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use App\Models\Employee;
use App\Models\Products;

class InventoryMovementsController extends Controller
{
    public function index()
    {
        $movements = InventoryMovement::with(['product', 'employee', 'supplier'])->latest()->get(); 
        $employees = Employee::all();
        $products = Products::all();

        return view('inventory_movements.index', compact('movements', 'employees', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'employee_id' => 'required|exists:employees,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'balance_forwarded' => 'required|integer',
            'pull_out' => 'required|integer',
            'new_balance' => 'required|integer',
            'new_luto' => 'required|integer',
            'total_inventory' => 'required|integer'
        ]);

        InventoryMovement::create($request->all());

        return redirect()->route('inventory_movements.index')->with('success', 'Inventory movement recorded.');
    }

    public function destroy($id)
    {
        $movement = InventoryMovement::findOrFail($id);
        $movement->delete();
        
        return redirect()->route('inventory.status')->with('success', 'Inventory Movement Deleted!');
    }

}

