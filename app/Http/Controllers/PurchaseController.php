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
use App\Services\SystemNotificationService;
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
        $suppliers = Supplier::where('status', 'Active')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')
            ->where('inventory_type', 'Raw Material')
            ->get();
        $employees = Employee::all();
        return view('purchases.create', compact('suppliers', 'products', 'employees'));
    }

    public function receivingMatching($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product', 'inventoryReceivings'])->findOrFail($id);
        
        // Find approved receivings from the same supplier that are not yet linked to this PO
        // and are not fully linked to other POs (optional complexity, keeping it simple for now)
        $availableReceivings = InventoryReceiving::with(['supplier', 'items.product'])
            ->where('supplier_id', $purchase->supplier_id)
            ->where('status', 'Approved')
            ->whereDoesntHave('purchases', function($q) use ($id) {
                $q->where('purchase_id', $id);
            })
            ->latest()
            ->get();

        return view('purchases.receiving-matching', compact('purchase', 'availableReceivings'));
    }

    public function linkReceiving(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $receiving = InventoryReceiving::findOrFail($request->inventory_receiving_id);

        if ($receiving->supplier_id !== $purchase->supplier_id) {
            return redirect()->back()->withErrors(['error' => 'Supplier mismatch between PO and Receiving record.']);
        }

        $purchase->linkReceiving(
            $receiving, 
            $receiving->total_items, 
            $receiving->total_cost
        );

        return redirect()->route('purchases.show', $purchase)->with('success', 'Artisan shipment linked successfully.');
    }

    public function unlinkReceiving(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $receiving = InventoryReceiving::findOrFail($request->inventory_receiving_id);

        $purchase->unlinkReceiving($receiving);

        return redirect()->route('purchases.show', $purchase)->with('success', 'Shipment unlinked from record.');
    }

    public function markAsReceived($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->status = 'Complete';
        $purchase->save();

        return redirect()->route('purchases.index')->with('success', 'Purchase order marked as received.');
    }

    public function cancel($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->status = 'Cancelled';
        $purchase->save();

        return redirect()->route('purchases.index')->with('success', 'Purchase order cancelled.');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'purchase_date' => 'required|date',
                'expected_delivery_date' => 'nullable|date|after_or_equal:purchase_date',
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
                'expected_delivery_date' => $request->expected_delivery_date,
                'supplier_id' => $request->supplier_id,
                'employee_id' => $request->employee_id,
                'reference' => 'PUR-' . now()->format('YmdHis'),
                'status' => 'Pending Approval',
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

                // Disable stock update trigger during PO creation
                // We use a temporary flag or just handle it if needed
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

            SystemNotificationService::notifyRoles(
                ['admin'],
                'po_pending_approval',
                'Purchase order pending approval',
                "Purchase Order {$purchase->po_number} is waiting for approval.",
                route('purchases.show', $purchase)
            );

            DB::commit();
            return redirect()->route('purchases.show', $purchase->id)->with('success', 'Purchase order created successfully');

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

        if (!in_array($purchase->status, ['Draft', 'Pending', 'Pending Approval', 'Rejected'], true)) {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot edit approved, ordered, received, or cancelled purchases']);
        }

        $suppliers = Supplier::all();
        $products = Product::where('status', 'Active')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        if (!in_array($purchase->status, ['Draft', 'Pending', 'Pending Approval', 'Rejected'], true)) {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot edit approved, ordered, received, or cancelled purchases']);
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
                'status' => 'Pending Approval',
                'rejected_by' => null,
                'rejected_date' => null,
                'rejection_reason' => null,
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

            SystemNotificationService::notifyRoles(
                ['admin'],
                'po_pending_approval',
                'Purchase order pending approval',
                "Purchase Order {$purchase->po_number} was updated and is waiting for approval.",
                route('purchases.show', $purchase)
            );

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

        if (!in_array($purchase->status, ['Draft', 'Pending', 'Pending Approval', 'Rejected'], true)) {
            return redirect()->route('purchases.show', $purchase)->withErrors(['error' => 'Cannot archive approved, ordered, received, or cancelled purchases']);
        }

        $purchase->update(['status' => 'Cancelled']);

        return redirect()->route('purchases.index')->with('success', 'Purchase order cancelled successfully');
    }

    public function approve(Purchase $purchase)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            return redirect()->back()->withErrors(['error' => 'Only an owner/admin can approve purchase orders.']);
        }

        if ($purchase->status !== 'Pending Approval') {
            return redirect()->back()->withErrors(['error' => 'Only purchase orders pending approval can be approved.']);
        }

        $purchase->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
            'approved_date' => now(),
        ]);

        SystemNotificationService::notifyRoles(
            ['manager'],
            'po_approved',
            'Purchase order approved',
            "Purchase Order {$purchase->po_number} has been approved.",
            route('purchases.show', $purchase)
        );

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order approved.');
    }

    public function reject(Request $request, Purchase $purchase)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            return redirect()->back()->withErrors(['error' => 'Only an owner/admin can reject purchase orders.']);
        }

        if ($purchase->status !== 'Pending Approval') {
            return redirect()->back()->withErrors(['error' => 'Only purchase orders pending approval can be rejected.']);
        }

        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        $purchase->update([
            'status' => 'Rejected',
            'rejected_by' => auth()->id(),
            'rejected_date' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        SystemNotificationService::notifyRoles(
            ['manager'],
            'po_rejected',
            'Purchase order rejected',
            "Purchase Order {$purchase->po_number} was rejected: {$request->rejection_reason}",
            route('purchases.show', $purchase)
        );

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order rejected.');
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
