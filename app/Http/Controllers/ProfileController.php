<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'admin') {
            return view('admin.profile', compact('user'));
        } elseif ($user->role == 'technician') {
            return view('techdashboard.profile', compact('user'));
        }
        return view('employee.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }
}
