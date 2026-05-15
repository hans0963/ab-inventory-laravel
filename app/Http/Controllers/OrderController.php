<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    public function create()
    {
        $walkInCustomer = Customer::where('name', 'Walk-in Customer')->first();
        return view('orders.create', [
            'customers' => Customer::all(),
            'products' => Product::all(),
            'default_customer_id' => $walkInCustomer ? $walkInCustomer->id : null
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        
        \DB::beginTransaction();

        try {
            $totalProducts = 0;
            $totalPrice = 0;

            foreach ($validated['product_id'] as $index => $productId) {
                $totalProducts += $validated['quantity'][$index];
                $totalPrice += $validated['quantity'][$index] * $validated['price'][$index];
            }

            // Get the logged in user's employee ID
            $employeeId = auth()->user()->employee_id ?? Employee::first()->id;

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'employee_id' => $employeeId,
                'order_date' => now(),
                'total' => $totalPrice,
                'payment_type' => $validated['payment_type'],
                'total_products' => $totalProducts,
                'order_status' => 'Completed', // Defaulting to Completed as it's a direct sale
            ]);

            foreach ($validated['product_id'] as $index => $productId) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $validated['quantity'][$index],
                    'unit_cost' => $validated['price'][$index],
                    'total' => $validated['quantity'][$index] * $validated['price'][$index],
                ]);
            }

            \DB::commit();

            return redirect()->route('orders.index')->with('success', '✅ Order created successfully!');
        
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Order creation failed: ' . $e->getMessage());
            $message = app()->environment('production') 
                ? '❌ Order failed: Please try again.' 
                : '❌ Order failed: ' . $e->getMessage();
            return back()->withErrors(['error' => $message]);
        }
    }

    public function index()
    {
        $orders = Order::with('customer')->latest()->paginate(10);
        
        // Metrics for the index view
        $todaysSales = Order::whereDate('order_date', now())->sum('total');
        $totalOrders = Order::count();
        $discounts = 0; // Placeholder for now as there's no discount column in orders table yet
        
        return view('orders.index', compact('orders', 'todaysSales', 'totalOrders', 'discounts'));
    }

    public function show($id)
    {
        $order = Order::with(['customer', 'orderDetails.product'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}

