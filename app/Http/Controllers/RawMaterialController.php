<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::paginate(10);
        $today = Carbon::today();
        
        // Reset nearExpiryCount to 0 as requested
        $nearExpiryCount = 0; 
        
        $expiredCount = RawMaterial::where('expiration_date', '<', $today)->count();

        return view('raw-materials.index', compact(
            'materials',
            'nearExpiryCount',
            'expiredCount'
        ));
    }

    public function stockInView()
    {
        $materials = RawMaterial::all();
        return view('raw-materials.stock-in', compact('materials'));
    }

    public function processStockIn(Request $request)
    {
        $request->validate([
            'material_id' => 'required|array',
            'material_id.*' => 'required|exists:raw_materials,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        \DB::beginTransaction();
        try {
            foreach ($request->material_id as $index => $id) {
                $material = RawMaterial::findOrFail($id);
                $qty = $request->quantity[$index];

                $material->increment('quantity', $qty);

                RawMaterialMovement::create([
                    'raw_material_id' => $id,
                    'user_id' => auth()->id(),
                    'quantity' => $qty,
                    'type' => 'IN',
                    'reason' => $request->reason,
                    'date' => now(),
                ]);
            }
            \DB::commit();
            return redirect()->route('raw-materials.index')->with('success', 'Stock in processed successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function stockOutView()
    {
        $materials = RawMaterial::where('quantity', '>', 0)->get();
        return view('raw-materials.stock-out', compact('materials'));
    }

    public function processStockOut(Request $request)
    {
        $request->validate([
            'material_id' => 'required|array',
            'material_id.*' => 'required|exists:raw_materials,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        \DB::beginTransaction();
        try {
            foreach ($request->material_id as $index => $id) {
                $material = RawMaterial::findOrFail($id);
                $qty = $request->quantity[$index];

                if ($material->quantity < $qty) {
                    throw new \Exception("Insufficient stock for {$material->material_name}");
                }

                $material->decrement('quantity', $qty);

                RawMaterialMovement::create([
                    'raw_material_id' => $id,
                    'user_id' => auth()->id(),
                    'quantity' => -$qty,
                    'type' => 'OUT',
                    'reason' => $request->reason,
                    'date' => now(),
                ]);
            }
            \DB::commit();
            return redirect()->route('raw-materials.index')->with('success', 'Stock out processed successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function adjust(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:IN,OUT',
            'reason' => 'nullable|string|max:255',
        ]);

        $qty = $request->quantity;
        
        if ($request->type === 'OUT') {
            if ($rawMaterial->quantity < $qty) {
                return back()->with('error', 'Insufficient stock for ' . $rawMaterial->material_name);
            }
            $rawMaterial->decrement('quantity', $qty);
        } else {
            $rawMaterial->increment('quantity', $qty);
        }

        RawMaterialMovement::create([
            'raw_material_id' => $rawMaterial->id,
            'user_id' => auth()->id(),
            'quantity' => $request->type === 'IN' ? $qty : -$qty,
            'type' => $request->type,
            'reason' => $request->reason,
            'date' => now(),
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Stock ' . $request->type . ' successful for ' . $rawMaterial->material_name);
    }

    public function create() { return view('raw-materials.create'); }
    public function store(Request $request) { return redirect()->route('raw-materials.index'); }
    public function edit($id) { return view('raw-materials.edit'); }
    public function update(Request $request, $id) { return redirect()->route('raw-materials.index'); }
    public function destroy($id) { return redirect()->route('raw-materials.index'); }
}
