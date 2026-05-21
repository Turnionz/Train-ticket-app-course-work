<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('employees.index', ['employees' => Employee::with([
            'user',
            'crew.assignments'
        ])->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('operator-level');

        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required',
            'type' => 'nullable',
            'password' => 'required'
        ]);

        $employee = Employee::create([
            'employee_type' => $request->type
        ]);

        $employee->user()->create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'role' => User::$role[array_search($request->role, User::$role)]
        ]);

        return redirect()->route('employees.show', $employee)->with('success', 'Працівника було створено успішно');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {

        $employee->load([
            'crew.assignments' => function ($query) {
                $query->whereHas('trip', function ($tripQuery) {
                    $tripQuery->where('depart_time', '>=', now()->subHours(24));
                })->with('trip');
            }
        ]);

        return view('employees.show', ['employee' => $employee]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit() {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Employee $employee, Request $request)
    {
        Gate::authorize('operator-level');

        $validated = $request->validate([
            'crew_id' => 'integer',
            'id' => 'integer'
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Дія була виконана успішно');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        Gate::authorize('operator-level');

        $employee->user->delete();
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Працівник був видалений');
    }
}
