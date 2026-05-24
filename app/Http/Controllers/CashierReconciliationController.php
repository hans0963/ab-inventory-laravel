<?php

namespace App\Http\Controllers;

use App\Models\CashierReconciliation;
use App\Models\Employee;
use App\Models\Sale;
use App\Services\SystemNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashierReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = CashierReconciliation::with(['employee', 'reviewer'])->latest('date_to')->latest();

        if (! $user->hasRole(['admin', 'manager'])) {
            $employee = $this->currentEmployee($request);
            $query->where('employee_id', $employee->id);
        } elseif ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reconciliations = $query->paginate(12)->withQueryString();
        $employees = $user->hasRole(['admin', 'manager'])
            ? Employee::whereHas('user', fn ($query) => $query->where('role', 'cashier'))->orderBy('employee_name')->get()
            : collect();

        return view('cashier-reconciliations.index', compact('reconciliations', 'employees'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isCashier(), 403);

        $employee = $this->currentEmployee($request);
        [$periodType, $dateFrom, $dateTo] = $this->periodFromRequest($request);
        $summary = $this->summaryFor($employee, $dateFrom, $dateTo);
        $recentSales = $this->salesFor($employee, $dateFrom, $dateTo)
            ->with(['product', 'customer'])
            ->latest()
            ->limit(10)
            ->get();

        return view('cashier-reconciliations.create', compact(
            'employee',
            'periodType',
            'dateFrom',
            'dateTo',
            'summary',
            'recentSales'
        ));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isCashier(), 403);

        $employee = $this->currentEmployee($request);

        $validated = $request->validate([
            'period_type' => ['required', 'in:daily,monthly,yearly,custom'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'opening_cash' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'actual_cash_count' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'cashier_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $dateFrom = Carbon::parse($validated['date_from'])->toDateString();
        $dateTo = Carbon::parse($validated['date_to'])->toDateString();
        $summary = $this->summaryFor($employee, $dateFrom, $dateTo, (float) $validated['opening_cash']);
        $actualCash = (float) $validated['actual_cash_count'];

        $reconciliation = CashierReconciliation::create([
            'employee_id' => $employee->id,
            'user_id' => $request->user()->id,
            'period_type' => $validated['period_type'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'opening_cash' => $validated['opening_cash'],
            'cash_sales_total' => $summary['cash_sales_total'],
            'non_cash_sales_total' => $summary['non_cash_sales_total'],
            'voided_cash_total' => $summary['voided_cash_total'],
            'total_sales' => $summary['total_sales'],
            'transaction_count' => $summary['transaction_count'],
            'expected_cash' => $summary['expected_cash'],
            'actual_cash_count' => $actualCash,
            'variance' => $actualCash - $summary['expected_cash'],
            'cashier_notes' => $validated['cashier_notes'] ?? null,
        ]);

        SystemNotificationService::notifyRoles(
            ['admin', 'manager'],
            'cashier_reconciliation_submitted',
            'Cashier reconciliation submitted',
            "{$employee->employee_name} submitted a {$validated['period_type']} reconciliation with variance PHP " . number_format($actualCash - $summary['expected_cash'], 2) . ".",
            route('cashier-reconciliations.show', $reconciliation)
        );

        return redirect()->route('cashier-reconciliations.show', $reconciliation)
            ->with('success', 'Cashier reconciliation submitted successfully.');
    }

    public function show(Request $request, CashierReconciliation $cashierReconciliation)
    {
        $this->authorizeReconciliationAccess($request, $cashierReconciliation);

        $cashierReconciliation->load(['employee.user', 'reviewer']);
        $recentSales = $this->salesFor(
            $cashierReconciliation->employee,
            $cashierReconciliation->date_from->toDateString(),
            $cashierReconciliation->date_to->toDateString()
        )->with(['product', 'customer'])->latest()->get();
        $summary = $this->summaryFor(
            $cashierReconciliation->employee,
            $cashierReconciliation->date_from->toDateString(),
            $cashierReconciliation->date_to->toDateString(),
            (float) $cashierReconciliation->opening_cash
        );

        return view('cashier-reconciliations.show', compact('cashierReconciliation', 'recentSales', 'summary'));
    }

    public function review(Request $request, CashierReconciliation $cashierReconciliation)
    {
        abort_unless($request->user()->hasRole(['admin', 'manager']), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:Approved,Flagged'],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $cashierReconciliation->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        SystemNotificationService::notifyUser(
            $cashierReconciliation->user_id,
            'cashier_reconciliation_reviewed',
            "Reconciliation {$validated['status']}",
            "Your reconciliation for {$cashierReconciliation->date_from->format('M d, Y')} - {$cashierReconciliation->date_to->format('M d, Y')} was {$validated['status']}.",
            route('cashier-reconciliations.show', $cashierReconciliation)
        );

        return redirect()->route('cashier-reconciliations.show', $cashierReconciliation)
            ->with('success', 'Reconciliation review saved.');
    }

    private function currentEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        abort_unless($employee, 403, 'Your account is not linked to an employee record.');

        return $employee;
    }

    private function periodFromRequest(Request $request): array
    {
        $periodType = $request->input('period_type', 'daily');
        $baseDate = Carbon::parse($request->input('date', today()->toDateString()));

        if ($periodType === 'monthly') {
            return [$periodType, $baseDate->copy()->startOfMonth()->toDateString(), $baseDate->copy()->endOfMonth()->toDateString()];
        }

        if ($periodType === 'yearly') {
            return [$periodType, $baseDate->copy()->startOfYear()->toDateString(), $baseDate->copy()->endOfYear()->toDateString()];
        }

        if ($periodType === 'custom') {
            $dateFrom = Carbon::parse($request->input('date_from', today()->toDateString()))->toDateString();
            $dateTo = Carbon::parse($request->input('date_to', $dateFrom))->toDateString();

            return [$periodType, $dateFrom, $dateTo];
        }

        return ['daily', $baseDate->toDateString(), $baseDate->toDateString()];
    }

    private function summaryFor(Employee $employee, string $dateFrom, string $dateTo, float $openingCash = 0): array
    {
        $activeSales = $this->salesFor($employee, $dateFrom, $dateTo)->where('void_status', '!=', 'Voided');
        $voidedSales = $this->salesFor($employee, $dateFrom, $dateTo)->where('void_status', 'Voided');

        $cashSales = (clone $activeSales)->where('payment_type', 'Cash')->sum('total_amount');
        $totalSales = (clone $activeSales)->sum('total_amount');
        $voidedCash = (clone $voidedSales)->where('payment_type', 'Cash')->sum('total_amount');

        return [
            'opening_cash' => $openingCash,
            'cash_sales_total' => (float) $cashSales,
            'non_cash_sales_total' => (float) ($totalSales - $cashSales),
            'voided_cash_total' => (float) $voidedCash,
            'total_sales' => (float) $totalSales,
            'transaction_count' => (clone $activeSales)->count(),
            'expected_cash' => (float) ($openingCash + $cashSales),
            'ewallet_total' => (float) (clone $activeSales)->where('payment_type', 'E-Wallet')->sum('total_amount'),
            'card_total' => (float) (clone $activeSales)->where('payment_type', 'Credit Card')->sum('total_amount'),
            'credit_total' => (float) (clone $activeSales)->where('payment_type', 'Credit/Loan')->sum('total_amount'),
            'voided_count' => (clone $voidedSales)->count(),
        ];
    }

    private function salesFor(Employee $employee, string $dateFrom, string $dateTo)
    {
        return Sale::where('employee_id', $employee->id)
            ->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
    }

    private function authorizeReconciliationAccess(Request $request, CashierReconciliation $reconciliation): void
    {
        if ($request->user()->hasRole(['admin', 'manager'])) {
            return;
        }

        $employee = $this->currentEmployee($request);
        abort_unless($reconciliation->employee_id === $employee->id, 403);
    }
}
