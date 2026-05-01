<?php

namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('suppliers_name', 'LIKE', "%{$search}%")
                  ->orWhere('suppliers_company', 'LIKE', "%{$search}%")
                  ->orWhere('suppliers_email', 'LIKE', "%{$search}%");
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
        $validatedData = $request->validate([
            'suppliers_name' => 'required|string|max:255',
            'suppliers_company' => 'nullable|string|max:255',
            'suppliers_email' => 'nullable|email|unique:suppliers,suppliers_email',
            'suppliers_phone' => 'nullable|string|max:20',
            'suppliers_address' => 'nullable|string|max:255',
        ]);

        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
