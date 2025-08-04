<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employees;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    // GET /api/employees - All employees
    public function index()
    {
        return response()->json(employees::all(), 200);
    }

    // POST /api/employees - Add new employee
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'age' => 'required|integer|min:18|max:100',
        //     'salary' => 'required|numeric|min:0',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        $employee = employees::create([
            'name'=>$request->name,
            'age'=>$request->age,
            'salary'=>$request->salary
        ]);

        return response()->json([
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 201);
    }

    // GET /api/employees/{id} - Get one employee
    public function show($id)
    {
        $employee = employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }

    // PUT /api/employees/{id} - Update employee
    public function update(Request $request, $id)
    {
        $employee = employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update($validator->validated());

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    // DELETE /api/employees/{id} - Delete employee
    public function destroy($id)
    {
        $employee = employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
