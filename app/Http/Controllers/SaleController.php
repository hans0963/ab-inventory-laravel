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
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'LIKE', "%{$search}%")
                    ->orWhereIn('receipt_number', Sale::whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'LIKE', "%{$search}%");
                    })->select('receipt_number'));
            });
        }

        $query->whereIn('id', Sale::selectRaw('MIN(id)')->groupBy('receipt_number'));

        $sales = $query->latest()->paginate(15);
        $sales->getCollection()->each(function (Sale $sale) {
            $sale->receiptLines = Sale::with('product')
                ->where('receipt_number', $sale->receipt_number)
                ->orderBy('id')
                ->get();
        });

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
            $items = collect($validated['items'])
                ->groupBy('product_id')
                ->map(fn ($rows, $productId) => [
                    'product_id' => (int) $productId,
                    'sold' => (int) $rows->sum('sold'),
                ])
                ->values();

            $products = Product::whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (!$product || $product->inventory_type !== 'Finished Product') {
                    return redirect()->back()->withInput()->with('error', 'Selected product is invalid for sales.');
                }

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
                    return redirect()->back()->withInput()->with('error', "Cannot process sale for {$product->product_name}: {$reason}");
                }

                if ($product->quantity < $item['sold']) {
                    return redirect()->back()->withInput()->with('error', "Insufficient stock for {$product->product_name}. Current stock: {$product->quantity}, Requested: {$item['sold']}.");
                }

                $subtotal += (float) $product->selling_price * $item['sold'];
            }

            if (!$validated['customer_id']) {
                $walkInCustomer = Customer::firstOrCreate(
                    ['name' => 'Walk-in'],
                    ['phone' => null, 'email' => null, 'address' => null, 'customer_type' => 'Walk-in']
                );
                $validated['customer_id'] = $walkInCustomer->id;
            }

            $discountType = null;
            $applicableIds = [];
            $discountableSubtotal = $subtotal;
            if ($validated['discount_type_id'] ?? null) {
                $discountType = DiscountType::find($validated['discount_type_id']);
                $applicableIds = $discountType->applicable_ids ?? [];
                $isInDateRange = (!$discountType->start_date || $discountType->start_date->lte(today()))
                    && (!$discountType->end_date || $discountType->end_date->gte(today()));
                $discountableSubtotal = $items->sum(function ($item) use ($products, $discountType, $applicableIds) {
                    $product = $products->get($item['product_id']);
                    $isApplicable = $discountType->applicable_to === 'All'
                        || ($discountType->applicable_to === 'Category' && in_array($product->category_id, $applicableIds))
                        || ($discountType->applicable_to === 'Product' && in_array($product->id, $applicableIds));

                    return $isApplicable ? (float) $product->selling_price * $item['sold'] : 0;
                });

                if ($discountType->status !== 'Active' || !$isInDateRange || $discountableSubtotal <= 0 || $subtotal < $discountType->minimum_purchase_amount) {
                    return redirect()->back()->with('error', 'Selected discount is not applicable to this sale.');
                }

                if ($discountType->discount_type === 'Fixed Amount') {
                    $totalDiscount = min($discountableSubtotal, (float) $discountType->discount_value);
                } else {
                    $totalDiscount = $discountableSubtotal * ((float) $discountType->discount_value / 100);
                }
            } else {
                $totalDiscount = 0;
            }

            $afterDiscount = $subtotal - $totalDiscount;
            $validated['vat_type'] = SystemSetting::value('vat_type', 'Inclusive');
            $validated['vat_rate'] = (float) SystemSetting::value('vat_rate', $validated['vat_rate']);

            if ($validated['vat_type'] === 'Inclusive' && $validated['vat_rate'] > 0) {
                $totalVat = $afterDiscount - ($afterDiscount / (1 + ($validated['vat_rate'] / 100)));
                $totalNetSubtotal = $afterDiscount - $totalVat;
                $grandTotal = $afterDiscount;
            } else {
                $totalNetSubtotal = $afterDiscount;
                $totalVat = $afterDiscount * ($validated['vat_rate'] / 100);
                $grandTotal = $afterDiscount + $totalVat;
            }

            if ($validated['payment_type'] === 'Credit/Loan') {
                $customer = Customer::lockForUpdate()->findOrFail($validated['customer_id']);

                if (!$customer->isCreditCustomer()) {
                    return redirect()->back()->with('error', 'Selected customer is not marked as a Credit/Loan Customer.');
                }

                if ($customer->availableCredit() < $grandTotal) {
                    return redirect()->back()->with('error', 'Credit limit exceeded. Available credit: ' . number_format($customer->availableCredit(), 2));
                }

                $validated['credit_due_date'] = $customer->credit_due_date;
                $customer->increment('current_balance', $grandTotal);
            }

            $receiptNumber = Sale::generateReceiptNumber();
            $createdSales = collect();
            $remainingDiscount = round($totalDiscount, 2);
            $remainingVat = round($totalVat, 2);
            $remainingNetSubtotal = round($totalNetSubtotal, 2);
            $remainingGrandTotal = round($grandTotal, 2);
            $lastDiscountableIndex = 0;

            foreach ($items as $index => $item) {
                $product = $products->get($item['product_id']);
                $isDiscountable = !$discountType
                    || $discountType->applicable_to === 'All'
                    || ($discountType->applicable_to === 'Category' && in_array($product->category_id, $applicableIds))
                    || ($discountType->applicable_to === 'Product' && in_array($product->id, $applicableIds));

                if ($isDiscountable) {
                    $lastDiscountableIndex = $index;
                }
            }

            foreach ($items as $index => $item) {
                $product = $products->get($item['product_id']);
                $lineSubtotal = (float) $product->selling_price * $item['sold'];
                $isDiscountable = !$discountType
                    || $discountType->applicable_to === 'All'
                    || ($discountType->applicable_to === 'Category' && in_array($product->category_id, $applicableIds))
                    || ($discountType->applicable_to === 'Product' && in_array($product->id, $applicableIds));
                $discountRatio = $isDiscountable && $discountableSubtotal > 0 ? $lineSubtotal / $discountableSubtotal : 0;
                $totalRatio = $subtotal > 0 ? $lineSubtotal / $subtotal : 0;
                $isLast = $index === $items->count() - 1;
                $isLastDiscountable = $index === $lastDiscountableIndex;

                $lineDiscount = !$isDiscountable ? 0 : ($isLastDiscountable ? $remainingDiscount : round($totalDiscount * $discountRatio, 2));
                $lineVat = $isLast ? $remainingVat : round($totalVat * $totalRatio, 2);
                $lineNetSubtotal = $isLast ? $remainingNetSubtotal : round($totalNetSubtotal * $totalRatio, 2);
                $lineTotal = $isLast ? $remainingGrandTotal : round($grandTotal * $totalRatio, 2);

                $sale = Sale::create([
                    'product_id' => $product->id,
                    'employee_id' => $validated['employee_id'],
                    'customer_id' => $validated['customer_id'],
                    'date' => $validated['date'],
                    'sold' => $item['sold'],
                    'unit_price' => $product->selling_price,
                    'discount_type_id' => $validated['discount_type_id'] ?? null,
                    'discount_amount' => $lineDiscount,
                    'payment_type' => $validated['payment_type'],
                    'credit_due_date' => $validated['credit_due_date'] ?? null,
                    'vat_rate' => $validated['vat_rate'],
                    'vat_type' => $validated['vat_type'],
                    'vat_amount' => $lineVat,
                    'subtotal_amount' => $lineNetSubtotal,
                    'total_amount' => $lineTotal,
                    'receipt_number' => $receiptNumber,
                ]);

                $quantityBefore = $product->quantity;
                $product->decrement('quantity', $item['sold']);
                StockMovementLogger::record(
                    $product,
                    $quantityBefore,
                    -$item['sold'],
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

                $remainingDiscount -= $lineDiscount;
                $remainingVat -= $lineVat;
                $remainingNetSubtotal -= $lineNetSubtotal;
                $remainingGrandTotal -= $lineTotal;
                $createdSales->push($sale);
            }

            return redirect()->route('sales.show', $createdSales->first()->id)
                ->with('success', 'Sale recorded successfully. Receipt: ' . $receiptNumber);
        });
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load(['product', 'employee', 'customer', 'discountType']);
        $receiptLines = Sale::with(['product.category', 'employee', 'customer', 'discountType'])
            ->where('receipt_number', $sale->receipt_number)
            ->orderBy('id')
            ->get();

        return view('sales.show', compact('sale', 'receiptLines'));
    }

    /**
     * Print receipt for a sale.
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load(['product', 'employee', 'customer', 'discountType']);
        $receiptLines = Sale::with(['product', 'employee', 'customer', 'discountType'])
            ->where('receipt_number', $sale->receipt_number)
            ->orderBy('id')
            ->get();

        return view('sales.receipt', compact('sale', 'receiptLines'));
    }

    public function requestVoid(Request $request, Sale $sale)
    {
        $request->validate(['void_reason' => 'required|string|max:1000']);

        if ($sale->void_status !== 'Active') {
            return redirect()->back()->with('error', 'This sale already has a void action.');
        }

        Sale::where('receipt_number', $sale->receipt_number)->update([
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
            $receiptLines = Sale::where('receipt_number', $sale->receipt_number)
                ->lockForUpdate()
                ->get();

            foreach ($receiptLines as $line) {
                $product = Product::lockForUpdate()->findOrFail($line->product_id);
                $quantityBefore = $product->quantity;
                $product->increment('quantity', $line->sold);

                StockMovementLogger::record(
                    $product,
                    $quantityBefore,
                    $line->sold,
                    'IN',
                    'SALE VOID',
                    $line,
                    $line->employee_id,
                    Auth::id()
                );
            }

            if ($sale->payment_type === 'Credit/Loan' && $sale->customer) {
                $totalAmount = (float) $receiptLines->sum('total_amount');
                $sale->customer->decrement('current_balance', min((float) $sale->customer->current_balance, $totalAmount));
            }

            Sale::where('receipt_number', $sale->receipt_number)->update([
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
