<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Mail\NewUserCredentialsMail;

class UserController extends Controller
{
    // fetch users with pagination, search and filter by role
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $companies = Company::all();
        $departments = Department::all();

        return view('admin.usermanagement', compact('users', 'companies', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'speciality' => 'nullable|string',
            'supervisor_name' => 'nullable|string|max:255',
            'supervisor_email' => 'nullable|email',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $plainPassword = Str::random(10); // generate random password

        $speciality = $request->role === 'technician' ? $request->speciality : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'speciality' => $speciality,
            'password' => Hash::make($plainPassword),
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
        ]);

        try {
            Mail::to($user->email)->send(
                new NewUserCredentialsMail($user, $plainPassword)
            );
            return redirect()->back()->with('success', 'User Added Successfully and email sent');
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'User Added Successfully (email could not be sent: ' . $e->getMessage() . ')');
        }
    }
    public function edit(User $user)
    {
       return view('admin.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'speciality' => 'nullable|string',
            'supervisor_name' => 'nullable|string|max:255',
            'supervisor_email' => 'nullable|email',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $speciality = $request->role === 'technician' ? $request->speciality : null;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'speciality' => $speciality,
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('user.index')->with('success', 'User Updated Successfully');
    }

    public function toggleSuspend(User $user)
    {
        if ($user->status === 'active') {
            $user->update(['status' => 'suspended']);
            return redirect()->route('user.index')->with('success', 'User Suspended Successfully');
        } else {
            $user->update(['status' => 'active']);
            return redirect()->route('user.index')->with('success', 'User Activated Successfully');
        }
    }
}
