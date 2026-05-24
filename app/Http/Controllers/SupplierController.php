<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('suppliers_name', 'LIKE', "%{$search}%")
                    ->orWhere('suppliers_company', 'LIKE', "%{$search}%")
                    ->orWhere('suppliers_email', 'LIKE', "%{$search}%")
                    ->orWhere('suppliers_phone', 'LIKE', "%{$search}%")
                    ->orWhere('items_supplied', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateSupplier($request);

        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::with(['purchases' => function ($query) {
            $query->with('details.product')->latest('purchase_date');
        }])->findOrFail($id);

        $purchaseHistory = $supplier->purchases()->with('details.product')->latest('purchase_date')->paginate(10);
        $totalAmountPurchased = $supplier->purchases()
            ->where('status', '!=', 'Cancelled')
            ->sum('total_amount');
        $outstandingPayments = $supplier->payment_terms === 'Credit'
            ? $supplier->purchases()
                ->whereIn('status', ['Pending', 'Approved', 'Partial'])
                ->sum('total_amount')
            : 0;

        return view('suppliers.show', compact(
            'supplier',
            'purchaseHistory',
            'totalAmountPurchased',
            'outstandingPayments'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validatedData = $this->validateSupplier($request, $supplier);

        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    private function validateSupplier(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'suppliers_company' => ['required', 'string', 'max:255'],
            'suppliers_name' => ['required', 'string', 'max:255'],
            'suppliers_email' => [
                'nullable',
                'email',
                Rule::unique('suppliers', 'suppliers_email')->ignore($supplier?->id),
            ],
            'suppliers_phone' => ['nullable', 'string', 'max:20'],
            'suppliers_address' => ['nullable', 'string', 'max:1000'],
            'items_supplied' => ['nullable', 'string', 'max:1000'],
            'payment_terms' => ['required', Rule::in(['Cash', 'Credit'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);
    }
}
