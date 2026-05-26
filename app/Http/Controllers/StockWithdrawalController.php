<?php

namespace App\Http\Controllers;

use App\Models\StockWithdrawal;
use App\Models\StockWithdrawalItem;
use App\Models\RawMaterial;
use App\Http\Requests\StoreStockWithdrawalRequest;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = StockWithdrawal::with('items', 'createdBy', 'approvedBy');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('reason') && $request->reason !== '') {
            $query->where('reason', $request->reason);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $withdrawals = $query->latest()->paginate(10);
        return view('stock-withdrawal.index', compact('withdrawals'));
    }

    public function create()
    {
        $rawMaterials = RawMaterial::where('status', 'Active')
            ->where('quantity', '>', 0)
            ->orderBy('material_name')
            ->get();

        return view('stock-withdrawal.create', compact('rawMaterials'));
    }

    public function store(StoreStockWithdrawalRequest $request)
    {
        try {
            foreach ($request->items as $itemData) {
                $material = RawMaterial::findOrFail($itemData['raw_material_id']);
                if ($material->quantity < $itemData['quantity']) {
                    return redirect()->back()
                        ->with('error', "Insufficient stock for {$material->material_name}. Available: {$material->quantity}");
                }
            }

            DB::beginTransaction();

            $totalQuantity = 0;
            $totalValue = 0;
            foreach ($request->items as $itemData) {
                $totalQuantity += $itemData['quantity'];
                $totalValue += $itemData['quantity'] * $itemData['unit_price'];
            }

            $withdrawal = StockWithdrawal::create([
                'withdrawal_no' => StockWithdrawal::generateWithdrawalNo(),
                'date' => $request->date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_quantity' => $totalQuantity,
                'total_value' => $totalValue,
                'created_by' => Auth::id(),
                'created_date' => now()->toDateString(),
                'status' => 'Pending'
            ]);

            foreach ($request->items as $itemData) {
                $itemValue = $itemData['quantity'] * $itemData['unit_price'];
                StockWithdrawalItem::create([
                    'stock_withdrawal_id' => $withdrawal->id,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_value' => $itemValue
                ]);
            }

            DB::commit();

            SystemNotificationService::notifyRoles(
                ['admin', 'manager'],
                'stock_withdrawal_pending',
                'Stock withdrawal needs approval',
                "Stock Withdrawal {$withdrawal->withdrawal_no} was submitted for approval.",
                route('stock-withdrawal.show', $withdrawal)
            );

            return redirect()->route('stock-withdrawal.show', $withdrawal->id)
                           ->with('success', 'Stock Withdrawal created successfully. Reference: ' . $withdrawal->withdrawal_no);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create withdrawal: ' . $e->getMessage());
        }
    }

    public function show(StockWithdrawal $withdrawal)
    {
        $withdrawal->load('items.rawMaterial', 'createdBy', 'approvedBy');
        return view('stock-withdrawal.show', compact('withdrawal'));
    }

    public function approve(StockWithdrawal $withdrawal)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $withdrawal->approve(Auth::user());

            DB::commit();

            SystemNotificationService::notifyUser(
                $withdrawal->created_by,
                'stock_withdrawal_approved',
                'Stock withdrawal approved',
                "Stock Withdrawal {$withdrawal->withdrawal_no} has been approved.",
                route('stock-withdrawal.show', $withdrawal)
            );

            return redirect()->route('stock-withdrawal.show', $withdrawal->id)
                           ->with('success', 'Stock Withdrawal approved and stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    public function reject(StockWithdrawal $withdrawal)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $withdrawal->reject(Auth::user());

        SystemNotificationService::notifyUser(
            $withdrawal->created_by,
            'stock_withdrawal_rejected',
            'Stock withdrawal rejected',
            "Stock Withdrawal {$withdrawal->withdrawal_no} has been rejected.",
            route('stock-withdrawal.show', $withdrawal)
        );

        return redirect()->route('stock-withdrawal.show', $withdrawal->id)
                       ->with('success', 'Stock Withdrawal rejected.');
    }

    public function destroy(StockWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'Pending') {
            return redirect()->back()->with('error', 'Cannot delete approved or rejected withdrawal records.');
        }

        $withdrawal->delete();

        return redirect()->route('stock-withdrawal.index')
                       ->with('success', 'Stock Withdrawal deleted successfully.');
    }
}
