<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionOutController extends Controller
{
    public function index() { return view('production-out.index'); }
    public function create() { return view('production-out.create'); }
    public function store(Request $request) { return redirect()->route('production-out.index'); }
    public function edit($id) { return view('production-out.edit'); }
    public function update(Request $request, $id) { return redirect()->route('production-out.index'); }
    public function destroy($id) { return redirect()->route('production-out.index'); }
}
