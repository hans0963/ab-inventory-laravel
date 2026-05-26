<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = RawMaterial::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('material_name', 'like', "%{$request->search}%")
                    ->orWhere('type', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        $today = Carbon::today();
        
        $nearExpiryCount = RawMaterial::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '>=', $today)
            ->whereDate('expiration_date', '<=', $today->copy()->addDays(7))
            ->count();
        
        $expiredCount = RawMaterial::where('expiration_date', '<', $today)->count();

        return view('raw-materials.index', compact(
            'materials',
            'nearExpiryCount',
            'expiredCount'
        ));
    }

    public function adjust(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:In,Out',
            'reason' => 'nullable|string|max:255',
        ]);

        $qty = $request->quantity;
        
        if ($request->type === 'Out') {
            if ($rawMaterial->quantity < $qty) {
                return back()->with('error', 'Insufficient stock for ' . $rawMaterial->material_name);
            }
            $rawMaterial->decrement('quantity', $qty);
        } else {
            $rawMaterial->increment('quantity', $qty);
        }

        RawMaterialMovement::create([
            'raw_material_id' => $rawMaterial->id,
            'quantity' => $request->type === 'In' ? $qty : -$qty,
            'type' => $request->type,
            'notes' => $request->reason,
            'date' => now()->toDateString(),
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Stock ' . $request->type . ' successful for ' . $rawMaterial->material_name);
    }

    public function create()
    {
        return view('raw-materials.create');
    }

    public function show(RawMaterial $rawMaterial)
    {
        return redirect()->route('raw-materials.edit', $rawMaterial);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMaterial($request);

        DB::transaction(function () use ($validated) {
            $material = RawMaterial::create($validated);

            if ($material->quantity > 0) {
                RawMaterialMovement::create([
                    'raw_material_id' => $material->id,
                    'quantity' => $material->quantity,
                    'type' => 'In',
                    'notes' => 'Initial stock',
                    'date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('raw-materials.index')->with('success', 'Raw material created successfully.');
    }

    public function edit(RawMaterial $rawMaterial)
    {
        return view('raw-materials.edit', compact('rawMaterial'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $this->validateMaterial($request);
        $oldQuantity = $rawMaterial->quantity;

        DB::transaction(function () use ($rawMaterial, $validated, $oldQuantity) {
            $rawMaterial->update($validated);
            $difference = $rawMaterial->quantity - $oldQuantity;

            if ($difference !== 0) {
                RawMaterialMovement::create([
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $difference,
                    'type' => 'Adjustment',
                    'notes' => 'Manual quantity update',
                    'date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('raw-materials.index')->with('success', 'Raw material updated successfully.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->update(['status' => 'Inactive']);

        return redirect()->route('raw-materials.index')->with('success', 'Raw material archived successfully.');
    }

    private function validateMaterial(Request $request): array
    {
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0|max:999999',
            'unit' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'expiry_alert_days' => 'nullable|integer|min:0|max:365',
            'status' => 'required|in:Active,Inactive',
            'stock_alert_threshold' => 'required|integer|min:0|max:999999',
            'reorder_level' => 'nullable|integer|min:0|max:999999',
            'reorder_quantity' => 'nullable|integer|min:0|max:999999',
        ]);

        $validated['unit'] = $validated['unit'] ?: 'kg';

        return $validated;
    }
}
