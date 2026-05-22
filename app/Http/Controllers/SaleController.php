<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\DiscountType;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['product', 'employee', 'customer', 'discountType']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(15);
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::where('status', 'Active')
                          ->where('inventory_type', 'Finished Product')
                          ->get();
        
        $employees = Employee::all();
        $customers = Customer::all();
        $discountTypes = DiscountType::where('status', 'Active')->get();
        
        // Get or create "Walk-in" customer
        $walkInCustomer = Customer::firstOrCreate(
            ['name' => 'Walk-in'],
            ['phone' => null, 'email' => null, 'address' => null]
        );

        // Get current user's employee record
        $currentEmployee = Employee::where('user_id', Auth::id())->first();

        return view('sales.create', compact(
            'products',
            'employees',
            'customers',
            'discountTypes',
            'walkInCustomer',
            'currentEmployee'
        ));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $validated = $request->validated();

        if ($validated['payment_type'] === 'GCash') {
            $validated['payment_type'] = 'E-Wallet';
        } elseif ($validated['payment_type'] === 'Card') {
            $validated['payment_type'] = 'Credit Card';
        }

        // Get product
        $product = Product::findOrFail($validated['product_id']);

        // Validate product availability
        if (!$product->isAvailableForSale()) {
            $reason = '';
            if ($product->status === 'Inactive') {
                $reason = 'Product is inactive';
            } elseif ($product->isExpired()) {
                $reason = 'Product has expired';
            } elseif ($product->isOutOfStock()) {
                $reason = 'Product is out of stock';
            }
            return redirect()->back()->with('error', 'Cannot process sale: ' . $reason);
        }

        // Validate stock availability
        if ($product->quantity < $validated['sold']) {
            return redirect()->back()->with('error', 'Insufficient stock. Available: ' . $product->quantity);
        }

        // Set default customer to Walk-in if not provided
        if (!$validated['customer_id']) {
            $walkInCustomer = Customer::where('name', 'Walk-in')->first();
            $validated['customer_id'] = $walkInCustomer->id;
        }

        // Calculate discount amount if discount type is provided
        $subtotal = $product->selling_price * $validated['sold'];
        if ($validated['discount_type_id']) {
            $discountType = DiscountType::find($validated['discount_type_id']);
            $validated['discount_amount'] = $subtotal * ($discountType->discount_percentage / 100);
        } else {
            $validated['discount_amount'] = 0;
        }

        // Calculate VAT
        $afterDiscount = $subtotal - $validated['discount_amount'];
        $validated['vat_amount'] = $afterDiscount * ($validated['vat_rate'] / 100);

        // Calculate total
        $validated['total_amount'] = $afterDiscount + $validated['vat_amount'];

        // Generate receipt number
        $validated['receipt_number'] = Sale::generateReceiptNumber();

        // Create the sale
        $sale = Sale::create($validated);

        // Deduct from product stock
        $product->decrement('quantity', $validated['sold']);

        return redirect()->route('sales.show', $sale->id)
                       ->with('success', 'Sale recorded successfully. Receipt: ' . $validated['receipt_number']);
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load(['product', 'employee', 'customer', 'discountType']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Print receipt for a sale.
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load(['product', 'employee', 'customer', 'discountType']);
        return view('sales.receipt', compact('sale'));
    }
}
