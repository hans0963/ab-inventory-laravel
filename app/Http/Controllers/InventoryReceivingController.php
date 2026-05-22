<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryReceivingRequest;
use App\Models\InventoryReceiving;
use App\Models\InventoryReceivingItem;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryReceivingController extends Controller
{
    /**
     * Display a listing of inventory receivings.
     */
    public function index(Request $request)
    {
        $query = InventoryReceiving::with('supplier', 'items.product', 'createdBy', 'approvedBy')
            ->orderBy('date', 'desc');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $receivings = $query->paginate(15);
        $suppliers = Supplier::where('status', '=', 'Active')->get();

        return view('inventory-receiving.index', compact('receivings', 'suppliers'));
    }

    /**
     * Show the form for creating a new inventory receiving.
     */
    public function create()
    {
        $suppliers = Supplier::where('status', '=', 'Active')->get();
        $products = Product::where('status', 'Active')
            ->where('inventory_type', 'Raw Material')
            ->get();
        $purchases = Purchase::where('status', 'Pending')->orWhere('status', 'Partial')->get();

        return view('inventory-receiving.create', compact('suppliers', 'products', 'purchases'));
    }

    /**
     * Store a newly created inventory receiving in storage.
     */
    public function store(StoreInventoryReceivingRequest $request)
    {
        $validated = $request->validated();

        $receiving = InventoryReceiving::create([
            'receiving_no' => InventoryReceiving::generateReceivingNo(),
            'date' => $validated['date'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_id' => $validated['purchase_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'total_items' => 0,
            'total_cost' => 0,
            'status' => 'Pending',
            'created_by' => auth()->id(),
            'created_date' => now()->toDateString()
        ]);

        $totalCost = 0;
        $totalItems = 0;

        foreach ($validated['items'] as $itemData) {
            $itemCost = $itemData['quantity_received'] * $itemData['unit_cost'];
            $totalCost += $itemCost;
            $totalItems += $itemData['quantity_received'];

            InventoryReceivingItem::create([
                'inventory_receiving_id' => $receiving->id,
                'product_id' => $itemData['product_id'],
                'batch_number' => $itemData['batch_number'] ?? null,
                'quantity_ordered' => $itemData['quantity_ordered'],
                'quantity_received' => $itemData['quantity_received'],
                'unit_cost' => $itemData['unit_cost'],
                'expiration_date' => $itemData['expiration_date'] ?? null,
                'condition' => $itemData['condition']
            ]);
        }

        $receiving->update([
            'total_cost' => $totalCost,
            'total_items' => $totalItems
        ]);

        return redirect()->route('inventory-receiving.show', $receiving)
            ->with('success', 'Inventory receiving recorded successfully.');
    }

    /**
     * Display the specified inventory receiving.
     */
    public function show(InventoryReceiving $inventoryReceiving)
    {
        $receiving = $inventoryReceiving->load('supplier', 'purchase', 'items.product', 'createdBy', 'approvedBy');
        
        return view('inventory-receiving.show', compact('receiving'));
    }

    /**
     * Show the form for editing the specified inventory receiving.
     */
    public function edit(InventoryReceiving $inventoryReceiving)
    {
        if ($inventoryReceiving->status !== 'Pending') {
            return redirect()->route('inventory-receiving.show', $inventoryReceiving)
                ->with('error', 'Only pending receivings can be edited.');
        }

        $receiving = $inventoryReceiving->load('items');
        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->get();
        $purchases = Purchase::where('status', 'Pending')->orWhere('status', 'Partial')->get();

        return view('inventory-receiving.edit', compact('receiving', 'suppliers', 'products', 'purchases'));
    }

    /**
     * Update the specified inventory receiving in storage.
     */
    public function update(StoreInventoryReceivingRequest $request, InventoryReceiving $inventoryReceiving)
    {
        if ($inventoryReceiving->status !== 'Pending') {
            return redirect()->route('inventory-receiving.show', $inventoryReceiving)
                ->with('error', 'Only pending receivings can be updated.');
        }

        $validated = $request->validated();

        $inventoryReceiving->update([
            'date' => $validated['date'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_id' => $validated['purchase_id'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        // Delete existing items
        $inventoryReceiving->items()->delete();

        $totalCost = 0;
        $totalItems = 0;

        foreach ($validated['items'] as $itemData) {
            $itemCost = $itemData['quantity_received'] * $itemData['unit_cost'];
            $totalCost += $itemCost;
            $totalItems += $itemData['quantity_received'];

            InventoryReceivingItem::create([
                'inventory_receiving_id' => $inventoryReceiving->id,
                'product_id' => $itemData['product_id'],
                'quantity_ordered' => $itemData['quantity_ordered'],
                'quantity_received' => $itemData['quantity_received'],
                'unit_cost' => $itemData['unit_cost'],
                'expiration_date' => $itemData['expiration_date'] ?? null,
                'condition' => $itemData['condition']
            ]);
        }

        $inventoryReceiving->update([
            'total_cost' => $totalCost,
            'total_items' => $totalItems
        ]);

        return redirect()->route('inventory-receiving.show', $inventoryReceiving)
            ->with('success', 'Inventory receiving updated successfully.');
    }

    /**
     * Remove the specified inventory receiving from storage.
     */
    public function destroy(InventoryReceiving $inventoryReceiving)
    {
        if ($inventoryReceiving->status !== 'Pending') {
            return redirect()->route('inventory-receiving.index')
                ->with('error', 'Only pending receivings can be deleted.');
        }

        $receiving_no = $inventoryReceiving->receiving_no;
        $inventoryReceiving->delete();

        return redirect()->route('inventory-receiving.index')
            ->with('success', "Inventory receiving $receiving_no deleted successfully.");
    }

    /**
     * Approve the specified inventory receiving.
     */
    public function approve(InventoryReceiving $inventoryReceiving)
    {
        if (!$inventoryReceiving->canBeApproved()) {
            return redirect()->route('inventory-receiving.show', $inventoryReceiving)
                ->with('error', 'This receiving cannot be approved.');
        }

        $inventoryReceiving->approve(auth()->user());

        return redirect()->route('inventory-receiving.show', $inventoryReceiving)
            ->with('success', 'Inventory receiving approved. Product quantities updated.');
    }

    /**
     * Reject the specified inventory receiving.
     */
    public function reject(InventoryReceiving $inventoryReceiving)
    {
        if ($inventoryReceiving->status !== 'Pending') {
            return redirect()->route('inventory-receiving.show', $inventoryReceiving)
                ->with('error', 'Only pending receivings can be rejected.');
        }

        $inventoryReceiving->reject(auth()->user());

        return redirect()->route('inventory-receiving.show', $inventoryReceiving)
            ->with('success', 'Inventory receiving rejected.');
    }
}
