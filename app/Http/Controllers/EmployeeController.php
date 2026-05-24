<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\InventoryReceiving;
use App\Models\ProductionIn;
use App\Models\ProductionOut;
use App\Models\StockWithdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $search = request('search');
        $employees = Employee::with('user')->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_email', 'like', "%{$search}%")
                    ->orWhere('employee_phone', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        })->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEmployee($request);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['employee_name'],
                'email' => $validated['employee_email'],
                'password' => Hash::make('arbees123'),
                'role' => $validated['role'],
                'email_verified_at' => now(),
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_name' => $validated['employee_name'],
                'employee_email' => $validated['employee_email'],
                'employee_phone' => $validated['employee_phone'],
                'position' => $validated['position'],
                'date_hired' => $validated['date_hired'],
                'emergency_contact' => $validated['emergency_contact'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee and login account created successfully with default password "arbees123".');
    }

    public function show(Employee $employee)
    {
        $employee->load('user');

        $recentTransactions = $this->recentTransactionsFor($employee);
        $activitySummary = $this->activitySummaryFor($employee);
        $loginHistory = $employee->user
            ? DB::table('sessions')
                ->where('user_id', $employee->user->id)
                ->orderByDesc('last_activity')
                ->limit(10)
                ->get()
            : collect();

        return view('employees.show', compact(
            'employee',
            'recentTransactions',
            'activitySummary',
            'loginHistory'
        ));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $this->validateEmployee($request, $employee);

        DB::transaction(function () use ($validated, $employee) {
            $employee->update([
                'employee_name' => $validated['employee_name'],
                'employee_email' => $validated['employee_email'],
                'employee_phone' => $validated['employee_phone'],
                'position' => $validated['position'],
                'date_hired' => $validated['date_hired'],
                'emergency_contact' => $validated['emergency_contact'],
                'status' => $validated['status'],
            ]);

            if ($employee->user) {
                $employee->user->update([
                    'name' => $validated['employee_name'],
                    'email' => $validated['employee_email'],
                    'role' => $validated['role'],
                ]);
            } else {
                $user = User::create([
                    'name' => $validated['employee_name'],
                    'email' => $validated['employee_email'],
                    'password' => Hash::make('arbees123'),
                    'role' => $validated['role'],
                    'email_verified_at' => now(),
                ]);

                $employee->update(['user_id' => $user->id]);
            }
        });

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['status' => 'Archived']);

        if ($employee->user) {
            $employee->user->delete();
        }

        return redirect()->route('employees.index')->with('success', 'Employee archived successfully.');
    }

    private function validateEmployee(Request $request, ?Employee $employee = null): array
    {
        return $request->validate([
            'employee_name' => ['required', 'string', 'max:100'],
            'employee_email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('employees', 'employee_email')->ignore($employee?->id),
                Rule::unique('users', 'email')->ignore($employee?->user_id),
            ],
            'employee_phone' => ['nullable', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:50'],
            'role' => ['required', Rule::in(['admin', 'manager', 'cashier', 'baker'])],
            'date_hired' => ['nullable', 'date'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Archived'])],
        ]);
    }

    private function activitySummaryFor(Employee $employee): array
    {
        $userId = $employee->user_id;

        return [
            'sales_count' => $employee->sales()->count(),
            'sales_total' => $employee->sales()->sum('total_amount'),
            'orders_count' => $employee->orders()->count(),
            'purchases_count' => $employee->purchases()->count(),
            'purchases_total' => $employee->purchases()->sum('total_amount'),
            'inventory_movements_count' => $employee->inventoryMovements()->count(),
            'approvals_count' => $userId
                ? InventoryReceiving::where('approved_by', $userId)->count()
                    + StockWithdrawal::where('approved_by', $userId)->count()
                    + ProductionIn::where('approved_by', $userId)->count()
                    + ProductionOut::where('approved_by', $userId)->count()
                : 0,
            'created_records_count' => $userId
                ? InventoryReceiving::where('created_by', $userId)->count()
                    + StockWithdrawal::where('created_by', $userId)->count()
                    + ProductionIn::where('created_by', $userId)->count()
                    + ProductionOut::where('created_by', $userId)->count()
                : 0,
        ];
    }

    private function recentTransactionsFor(Employee $employee)
    {
        $transactions = collect();

        $employee->sales()->with('product')->latest()->limit(5)->get()->each(function ($sale) use ($transactions) {
            $transactions->push([
                'date' => $sale->created_at,
                'type' => 'Sale',
                'reference' => $sale->receipt_number ?? 'Sale #' . $sale->id,
                'details' => optional($sale->product)->product_name ?? 'Product sale',
                'amount' => $sale->total_amount,
                'status' => 'Completed',
            ]);
        });

        $employee->orders()->latest()->limit(5)->get()->each(function ($order) use ($transactions) {
            $transactions->push([
                'date' => $order->created_at,
                'type' => 'Order',
                'reference' => 'Order #' . $order->id,
                'details' => $order->total_products . ' product(s)',
                'amount' => $order->total,
                'status' => $order->order_status,
            ]);
        });

        $employee->purchases()->with('supplier')->latest()->limit(5)->get()->each(function ($purchase) use ($transactions) {
            $transactions->push([
                'date' => $purchase->created_at,
                'type' => 'Purchase',
                'reference' => $purchase->po_number ?? $purchase->reference ?? 'Purchase #' . $purchase->id,
                'details' => optional($purchase->supplier)->suppliers_company ?? 'Supplier purchase',
                'amount' => $purchase->total_amount,
                'status' => $purchase->status,
            ]);
        });

        $employee->inventoryMovements()->with('product')->latest()->limit(5)->get()->each(function ($movement) use ($transactions) {
            $transactions->push([
                'date' => $movement->created_at,
                'type' => 'Inventory',
                'reference' => $movement->transaction_type ?? 'Movement #' . $movement->id,
                'details' => optional($movement->product)->product_name ?? 'Inventory movement',
                'amount' => null,
                'status' => $movement->transaction_type ?? 'Recorded',
            ]);
        });

        return $transactions->sortByDesc('date')->take(12)->values();
    }
}
