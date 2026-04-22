<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;
use App\Models\InventoryMovement;
use App\Models\Products;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\PurchaseDetail;
use function Symfony\Component\Clock\now;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'details.product'])->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all(); 
        $products = Products::all(); 
        $employees = Employee::all(); 
        return view('purchases.create', compact('suppliers', 'products', 'employees'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $request->merge([
            'purchase_date' => now()->format('Y-m-d H:i:s')
        ]);

        try {
            $validatedData = $request->validate([
                'purchase_date' => 'date_format:Y-m-d H:i:s',
                'supplier_id' => 'required|exists:suppliers,id',
                'employee_id' => 'required|exists:employees,id',
                'product_id' => 'required|array',
                'quantity' => 'required|array',
                'price' => 'required|array'
            ]);

            if (!$validatedData) {
                return redirect()->back()->withErrors($validatedData)->withInput();
            }

            $purchase = Purchase::create([
                'purchase_date' => $request->purchase_date,
                'supplier_id' => $request->supplier_id,
                'employee_id' => $request->employee_id,
                'reference' => $request->reference,
            ]);

            foreach ($request->product_id as $index => $productId) {
                $quantity = $request->quantity[$index];
                $price = $request->price[$index];

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price
                ]);

                $product = Products::find($productId);
                if ($product) {
                    $product->increment('quantity', $quantity);
                }

                InventoryMovement::updateOrCreate(
                    ['product_id' => $productId],
                    ['date' => now(), 'pull_out' => 0]
                );
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Purchase added successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Purchase Save Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }



    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'employee', 'details.product'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);
        $suppliers = Supplier::all();
        $products = Products::all();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'prices' => 'required|array'
        ]);

        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'purchase_date' => $request->purchase_date,
            'supplier_id' => $request->supplier_id,
            'reference' => $request->reference,
        ]);

        PurchaseDetail::where('purchase_id', $id)->delete();

        foreach ($request->products as $index => $productId) {
            $quantity = $request->quantities[$index];
            $price = $request->prices[$index];

            $purchase->details()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully');
    }


    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->details()->delete(); // Delete related purchase details
        $purchase->delete(); // Delete purchase record

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully');
    }
}
