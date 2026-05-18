<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $search = request('search');
        $employees = Employee::with('user')->when($search, function ($query, $search) {
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
            'employee_email' => 'required|email|max:50|unique:employees|unique:users,email',
            'employee_phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:50',
            'role' => 'required|in:manager,cashier',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create the User account
            $user = User::create([
                'name' => $validated['employee_name'],
                'email' => $validated['employee_email'],
                'password' => Hash::make('arbees123'),
                'role' => $validated['role'],
            ]);

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            // 2. Create the Employee record linked to the user
            Employee::create([
                'user_id' => $user->id,
                'employee_name' => $validated['employee_name'],
                'employee_email' => $validated['employee_email'],
                'employee_phone' => $validated['employee_phone'],
                'position' => $validated['position'],
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee and login account created successfully with default password "arbees123".');
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
            'role' => 'required|in:manager,cashier',
        ]);

        DB::transaction(function () use ($validated, $employee) {
            // Update the Employee record
            $employee->update([
                'employee_name' => $validated['employee_name'],
                'employee_email' => $validated['employee_email'],
                'employee_phone' => $validated['employee_phone'],
                'position' => $validated['position'],
            ]);

            // Update the linked User account if it exists
            if ($employee->user) {
                $employee->user->update([
                    'name' => $validated['employee_name'],
                    'email' => $validated['employee_email'],
                    'role' => $validated['role'],
                ]);
            }
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
