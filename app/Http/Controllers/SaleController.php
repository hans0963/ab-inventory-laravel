<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\DiscountType;
use App\Models\SystemSetting;
use App\Http\Requests\StoreSaleRequest;
use App\Services\StockMovementLogger;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $customers = Customer::where('status', 'Active')->orWhereNull('status')->get();
        $discountTypes = DiscountType::where('status', 'Active')
            ->where(function ($query) {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', today());
            })
            ->get();
        
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
        $currentEmployee = Employee::where('user_id', Auth::id())->first();

        if ($currentEmployee) {
            $validated['employee_id'] = $currentEmployee->id;
        }

        if ($validated['payment_type'] === 'GCash') {
            $validated['payment_type'] = 'E-Wallet';
        } elseif ($validated['payment_type'] === 'Card') {
            $validated['payment_type'] = 'Credit Card';
        }

        return DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            if (!$product->isAvailableForSale()) {
                $reason = '';
                if ($product->status === 'Inactive') {
                    $reason = 'Product is inactive';
                } elseif ($product->isExpired()) {
                    $reason = 'Product has expired';
                } elseif ($product->isAtReorderLevel()) {
                    $reason = 'Product reached reorder level and is unavailable';
                } elseif ($product->isOutOfStock()) {
                    $reason = 'Product is out of stock';
                }
                return redirect()->back()->with('error', 'Cannot process sale: ' . $reason);
            }

            if ($product->quantity < $validated['sold']) {
                return redirect()->back()->with('error', "Insufficient stock. Current stock: {$product->quantity}, Requested: {$validated['sold']}. Transaction cannot be completed.");
            }

            if (!$validated['customer_id']) {
                $walkInCustomer = Customer::firstOrCreate(
                    ['name' => 'Walk-in'],
                    ['phone' => null, 'email' => null, 'address' => null, 'customer_type' => 'Walk-in']
                );
                $validated['customer_id'] = $walkInCustomer->id;
            }

            $validated['unit_price'] = $product->selling_price;
            $subtotal = $product->selling_price * $validated['sold'];
            if ($validated['discount_type_id'] ?? null) {
                $discountType = DiscountType::find($validated['discount_type_id']);
                $applicableIds = $discountType->applicable_ids ?? [];
                $isInDateRange = (!$discountType->start_date || $discountType->start_date->lte(today()))
                    && (!$discountType->end_date || $discountType->end_date->gte(today()));
                $isApplicable = $discountType->applicable_to === 'All'
                    || ($discountType->applicable_to === 'Category' && in_array($product->category_id, $applicableIds))
                    || ($discountType->applicable_to === 'Product' && in_array($product->id, $applicableIds));

                if ($discountType->status !== 'Active' || !$isInDateRange || !$isApplicable || $subtotal < $discountType->minimum_purchase_amount) {
                    return redirect()->back()->with('error', 'Selected discount is not applicable to this sale.');
                }

                if ($discountType->discount_type === 'Fixed Amount') {
                    $validated['discount_amount'] = min($subtotal, (float) $discountType->discount_value);
                } else {
                    $validated['discount_amount'] = $subtotal * ((float) $discountType->discount_value / 100);
                }
            } else {
                $validated['discount_amount'] = 0;
            }

            $afterDiscount = $subtotal - $validated['discount_amount'];
            $validated['vat_type'] = SystemSetting::value('vat_type', 'Inclusive');
            $validated['vat_rate'] = (float) SystemSetting::value('vat_rate', $validated['vat_rate']);

            if ($validated['vat_type'] === 'Inclusive' && $validated['vat_rate'] > 0) {
                $validated['vat_amount'] = $afterDiscount - ($afterDiscount / (1 + ($validated['vat_rate'] / 100)));
                $validated['subtotal_amount'] = $afterDiscount - $validated['vat_amount'];
                $validated['total_amount'] = $afterDiscount;
            } else {
                $validated['subtotal_amount'] = $afterDiscount;
                $validated['vat_amount'] = $afterDiscount * ($validated['vat_rate'] / 100);
                $validated['total_amount'] = $afterDiscount + $validated['vat_amount'];
            }

            if ($validated['payment_type'] === 'Credit/Loan') {
                $customer = Customer::lockForUpdate()->findOrFail($validated['customer_id']);

                if (!$customer->isCreditCustomer()) {
                    return redirect()->back()->with('error', 'Selected customer is not marked as a Credit/Loan Customer.');
                }

                if ($customer->availableCredit() < $validated['total_amount']) {
                    return redirect()->back()->with('error', 'Credit limit exceeded. Available credit: ' . number_format($customer->availableCredit(), 2));
                }

                $validated['credit_due_date'] = $customer->credit_due_date;
                $customer->increment('current_balance', $validated['total_amount']);
            }

            $validated['receipt_number'] = Sale::generateReceiptNumber();

            $sale = Sale::create($validated);

            $quantityBefore = $product->quantity;
            $product->decrement('quantity', $validated['sold']);
            StockMovementLogger::record(
                $product,
                $quantityBefore,
                -$validated['sold'],
                'OUT',
                'SALE',
                $sale,
                $validated['employee_id'],
                Auth::id()
            );

            $product->refresh();
            if ($product->isAtReorderLevel()) {
                SystemNotificationService::notifyRoles(
                    ['admin', 'manager'],
                    'low_stock',
                    'Reorder level reached',
                    "{$product->product_name} reached reorder level. Current stock: {$product->quantity}.",
                    route('products.show', $product)
                );
            }

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Sale recorded successfully. Receipt: ' . $validated['receipt_number']);
        });
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

    public function requestVoid(Request $request, Sale $sale)
    {
        $request->validate(['void_reason' => 'required|string|max:1000']);

        if ($sale->void_status !== 'Active') {
            return redirect()->back()->with('error', 'This sale already has a void action.');
        }

        $sale->update([
            'void_status' => 'Pending',
            'void_reason' => $request->void_reason,
            'void_requested_by' => Auth::id(),
        ]);

        SystemNotificationService::notifyRoles(
            ['admin', 'manager'],
            'void_pending',
            'Void approval required',
            "Sale {$sale->receipt_number} is pending void approval.",
            route('sales.show', $sale)
        );

        return redirect()->back()->with('success', 'Void request submitted for approval.');
    }

    public function approveVoid(Sale $sale)
    {
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            return redirect()->back()->with('error', 'Only manager or admin can approve void requests.');
        }

        if ($sale->void_status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending void requests can be approved.');
        }

        DB::transaction(function () use ($sale) {
            $product = Product::lockForUpdate()->findOrFail($sale->product_id);
            $quantityBefore = $product->quantity;
            $product->increment('quantity', $sale->sold);

            StockMovementLogger::record(
                $product,
                $quantityBefore,
                $sale->sold,
                'IN',
                'SALE VOID',
                $sale,
                $sale->employee_id,
                Auth::id()
            );

            if ($sale->payment_type === 'Credit/Loan' && $sale->customer) {
                $sale->customer->decrement('current_balance', min((float) $sale->customer->current_balance, (float) $sale->total_amount));
            }

            $sale->update([
                'void_status' => 'Voided',
                'void_approved_by' => Auth::id(),
                'voided_at' => now(),
            ]);
        });

        SystemNotificationService::notifyUser(
            $sale->void_requested_by,
            'void_approved',
            'Void request approved',
            "Sale {$sale->receipt_number} was voided and stock was returned.",
            route('sales.show', $sale)
        );

        return redirect()->back()->with('success', 'Sale void approved and stock returned.');
    }
}
