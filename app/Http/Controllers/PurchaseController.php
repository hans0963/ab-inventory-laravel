<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\PurchaseDetail;
use App\Models\InventoryReceiving;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use function Symfony\Component\Clock\now;

class PurchaseController extends Controller
{
    public function index()
    {
        $query = Purchase::with(['supplier', 'details.product', 'createdBy']);

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by supplier
        if (request('supplier_id')) {
            $query->where('supplier_id', request('supplier_id'));
        }

        // Filter by date range
        if (request('date_from')) {
            $query->whereDate('purchase_date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('purchase_date', '<=', request('date_to'));
        }

        $purchases = $query->latest('purchase_date')->paginate(15);
        $suppliers = Supplier::all();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('status', 'Active')
            ->where('inventory_type', 'Raw Material')
            ->get();
        $employees = Employee::all();
        return view('purchases.create', compact('suppliers', 'products', 'employees'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'purchase_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'employee_id' => 'nullable|exists:employees,id',
                'product_id' => 'required|array|min:1',
                'quantity' => 'required|array|min:1',
                'price' => 'required|array|min:1',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Create purchase
            $purchase = new Purchase([
                'purchase_date' => $request->purchase_date,
                'supplier_id' => $request->supplier_id,
                'employee_id' => $request->employee_id,
                'reference' => 'PUR-' . now()->format('YmdHis'),
                'status' => 'Pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_date' => now(),
            ]);

            $purchase->save();

            // Generate PO number after purchase is created
            $purchase->po_number = Purchase::generatePONumber();
            $purchase->save();

            $total = 0;

            // Create purchase details
            foreach ($request->product_id as $index => $productId) {
                if (!isset($request->quantity[$index]) || !isset($request->price[$index])) {
                    continue;
                }

                $quantity = (int) $request->quantity[$index];
                $price = (float) $request->price[$index];
                $itemTotal = $quantity * $price;
                $total += $itemTotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ]);
            }

            // Update total amount
            $purchase->total_amount = $total;
            $purchase->save();

            DB::commit();
            return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Purchase Save Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with([
            'supplier',
            'employee',
            'details.product',
            'createdBy',
            'approvedBy',
            'inventoryReceivings',
        ])->findOrFail($id);

        $receivingProgress = $purchase->getReceptionProgress();
        $totalOrdered = $purchase->getTotalQuantityOrdered();
        $totalReceived = $purchase->getTotalQuantityReceived();

        return view('purchases.show', compact('purchase', 'receivingProgress', 'totalOrdered', 'totalReceived'));
    }

    public function edit($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        // Only allow editing pending purchases
        if ($purchase->status !== 'Pending') {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot edit completed or partial purchases']);
        }

        $suppliers = Supplier::all();
        $products = Product::where('status', 'Active')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== 'Pending') {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot edit non-pending purchases']);
        }

        $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'price' => 'required|array|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $purchase->update([
                'purchase_date' => $request->purchase_date,
                'supplier_id' => $request->supplier_id,
                'notes' => $request->notes,
            ]);

            PurchaseDetail::where('purchase_id', $id)->delete();

            $total = 0;
            foreach ($request->product_id as $index => $productId) {
                $quantity = (int) $request->quantity[$index];
                $price = (float) $request->price[$index];
                $itemTotal = $quantity * $price;
                $total += $itemTotal;

                $purchase->details()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ]);
            }

            $purchase->update(['total_amount' => $total]);

            DB::commit();
            return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Purchase Update Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        // Only allow deleting pending purchases
        if ($purchase->status !== 'Pending') {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot delete completed or partial purchases']);
        }

        DB::beginTransaction();

        try {
            PurchaseDetail::where('purchase_id', $id)->delete();
            $purchase->receivingLinks()->delete();
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase order deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Purchase Delete Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Link inventory receiving to purchase
     */
    public function linkReceiving(Request $request, $id)
    {
        Gate::authorize('viewAny', Purchase::class);

        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'inventory_receiving_id' => 'required|exists:inventory_receivings,id',
        ]);

        $receiving = InventoryReceiving::findOrFail($request->inventory_receiving_id);

        // Calculate items and amount received (Good condition only)
        $itemsReceived = $receiving->inventoryReceivingItems()
            ->where('condition', 'Good')
            ->count();

        $amountReceived = $receiving->inventoryReceivingItems()
            ->where('condition', 'Good')
            ->sum('total_cost');

        $purchase->linkReceiving($receiving, $itemsReceived, $amountReceived);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Inventory receiving linked to purchase order successfully');
    }

    /**
     * Unlink inventory receiving from purchase
     */
    public function unlinkReceiving(Request $request, $id)
    {
        Gate::authorize('viewAny', Purchase::class);

        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'inventory_receiving_id' => 'required|exists:inventory_receivings,id',
        ]);

        $receiving = InventoryReceiving::findOrFail($request->inventory_receiving_id);
        $purchase->unlinkReceiving($receiving);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Inventory receiving unlinked from purchase order');
    }

    /**
     * Show receiving matching view
     */
    public function receivingMatching($id)
    {
        $purchase = Purchase::with(['details.product', 'inventoryReceivings'])->findOrFail($id);
        $availableReceivings = InventoryReceiving::with('items.product')
            ->where('supplier_id', $purchase->supplier_id)
            ->where('status', 'Approved')
            ->whereNotIn('id', $purchase->inventoryReceivings->pluck('id'))
            ->get();

        return view('purchases.receiving-matching', compact('purchase', 'availableReceivings'));
    }

    /**
     * Show PO receiving progress report
     */
    public function receivingProgress()
    {
        $purchases = Purchase::with(['supplier', 'details', 'inventoryReceivings'])
            ->where('status', '!=', 'Complete')
            ->orderBy('purchase_date', 'desc')
            ->paginate(15);

        return view('purchases.receiving-progress', compact('purchases'));
    }
}
