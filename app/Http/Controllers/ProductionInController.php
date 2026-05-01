<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionInController extends Controller
{
    public function index() { return view('production-in.index'); }
    public function create() { return view('production-in.create'); }
    public function store(Request $request) { return redirect()->route('production-in.index'); }
    public function edit($id) { return view('production-in.edit'); }
    public function update(Request $request, $id) { return redirect()->route('production-in.index'); }
    public function destroy($id) { return redirect()->route('production-in.index'); }
}
