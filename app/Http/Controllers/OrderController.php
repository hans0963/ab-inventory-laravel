<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Products;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function create()
    {
        return view('orders.create', [
            'customers' => Customer::all(),
            'employees' => Employee::all(),
            'products' => Products::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'required|exists:employees,id',
            'order_date' => NOW(),
            'payment_type' => 'required|in:Cash,Card,Online',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ]);

        \DB::beginTransaction();

        try {
            $totalProducts = 0;
            $totalPrice = 0;

            foreach ($request->product_id as $index => $productId) {
                $totalProducts += $request->quantity[$index];
                $totalPrice += $request->quantity[$index] * $request->price[$index];
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'employee_id' => $request->employee_id,
                'order_date' => now(),
                'total' => $totalPrice,
                'total_products' => $totalProducts,
            ]);

            foreach ($request->product_id as $index => $productId) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $request->quantity[$index],
                    'unit_cost' => $request->price[$index],
                    'total' => $request->quantity[$index] * $request->price[$index],
                ]);
            }

            \DB::commit();

            return redirect()->route('orders.index')->with('success', '✅ Order created successfully!');
        
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors(['error' => '❌ Order failed: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $orders = Order::with('customer')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['customer', 'orderDetails.product'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

}

