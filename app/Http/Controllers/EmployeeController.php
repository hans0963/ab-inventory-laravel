<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $search = request('search');
        $employees = Employee::when($search, function ($query, $search) {
            return $query->where('employee_name', 'like', "%$search%");
        })->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:100',
            'employee_email' => 'required|email|max:50|unique:employees',
            'employee_phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:50',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:100',
            'employee_email' => 'required|email|max:50|unique:employees,employee_email,' . $employee->id,
            'employee_phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:50',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
