<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('customer_type', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->withCount('sales')
            ->withSum('sales as total_spent', 'total_amount')
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:Regular,Senior,PWD,VIP',
        ]);

        Customer::create($request->only(['name', 'email', 'phone', 'address', 'customer_type']));

        return redirect()->route('customers.index')->with('success', 'Customer added successfully');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:Regular,Senior,PWD,VIP',
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'address', 'customer_type']));

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales.product', 'sales.discountType']);

        $recentSales = $customer->sales()
            ->with(['product', 'discountType'])
            ->latest('date')
            ->take(10)
            ->get();

        $favoriteProducts = $customer->sales()
            ->selectRaw('product_id, SUM(sold) as total_quantity, SUM(total_amount) as total_spent')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        $discountHistory = $customer->sales()
            ->whereNotNull('discount_type_id')
            ->with('discountType')
            ->orderByDesc('date')
            ->take(10)
            ->get();

        return view('customers.show', compact('customer', 'recentSales', 'favoriteProducts', 'discountHistory'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }
}