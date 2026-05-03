<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
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
    public function create() { return view('raw-materials.create'); }
    public function store(Request $request) { return redirect()->route('raw-materials.index'); }
    public function edit($id) { return view('raw-materials.edit'); }
    public function update(Request $request, $id) { return redirect()->route('raw-materials.index'); }
    public function destroy($id) { return redirect()->route('raw-materials.index'); }
}
