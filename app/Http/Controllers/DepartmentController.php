<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'departments');

        $companiesQuery = Company::query();
        $departmentsQuery = Department::with('company');

        if ($request->filled('search')) {
            if ($tab === 'companies') {
                $companiesQuery->where('name', 'like', "%{$search}%");
            } else {
                $departmentsQuery->where('name', 'like', "%{$search}%");
            }
        }

        $companies = $companiesQuery->paginate(10, ['*'], 'companies_page')->withQueryString();
        $departments = $departmentsQuery->paginate(10, ['*'], 'departments_page')->withQueryString();

        return view('admin.dept', compact('companies', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create($request->all());

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update($request->all());

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }
}
