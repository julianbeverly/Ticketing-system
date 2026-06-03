<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
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

        return view('admin.usermanagement', compact('users'));
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
        ]);

        $plainPassword = Str::random(10); // generate random password

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'speciality' => $request->speciality,
            'password' => Hash::make($plainPassword),
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
        ]);

    Mail::to($user->email)->send(
        new NewUserCredentialsMail($user, $plainPassword)
    );

    return redirect()->back()->with('success', 'User Added Successfully and email sent');
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
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'speciality' => $request->speciality,
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
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
