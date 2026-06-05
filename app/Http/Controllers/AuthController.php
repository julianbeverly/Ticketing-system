<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
//    login and send otp
    public function login(Request $request)
    {
        // Validate login form
        $request->validate([
            'email'    => 'required|email',  
            'password' => 'required'
        ]);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials');
        }

        // Check if user is suspended
        if ($user->status === 'suspended') {
            return back()->with('error', 'Your account has been suspended. Please contact the administrator.');
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        // $otp = 111111;

        // Save OTP in session (NO DATABASE TABLE NEEDED)
        session([
            'otp_code'       => $otp,
            'otp_user_id'    => $user->id,
            'otp_expires_at' => now()->addMinutes(10) // OTP valid for 10 minutes
        ]);

        // Send OTP to email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Go to OTP page
        return redirect()->route('authentication.otp')
            ->with('success', 'Enter the OTP sent to your email');
    }

    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Update session
        session([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        // Send OTP to email
        Mail::to($user->email)->send(new OtpMail($otp));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }


    // verify otp and user role
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        // Check OTP
        if ($request->otp != session('otp_code')) {
            return back()->with('error', 'Invalid OTP');
        }

        // Check expiry
        if (now()->isAfter(session('otp_expires_at'))) {
            return back()->with('error', 'OTP expired');
        }

        // Login user
        Auth::loginUsingId(session('otp_user_id'));

        // Clear OTP session
        session()->forget([
            'otp_code',
            'otp_user_id',
            'otp_expires_at'
        ]);
// gets currently logged in user
        $user = Auth::user(); 
// This line creates a new session ID for the logged-in user
session()->regenerate();

// checks user role and redirects to the appropriate dashboard
if ($user->role == 'admin') {
    return redirect('/dash');
}

if ($user->role == 'technician') {
    return redirect('/techdash');
}

if ($user->role == 'employee') {
    return redirect('/employeedash');
}

        // Default fallback
        return redirect('/');
    }


 //    logout
    // public function logout()
    // {
    //     Auth::logout();
    //     return redirect('/login');
    // }

    public function showLogin()
{
    return view('authentication.login');
}

public function showOtp()
{
    return view('authentication.otp');
}

public function showForget()
{
    return view('authentication.forget');
}

public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'We could not find a user with that email address.');
    }
// generate reset token 
    $token = Str::random(64);
// stores token in database
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => $token,
            'created_at' => Carbon::now()
        ]
    );
// generates a password reset URL/link and stores
    $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
    // sends the reset link to the user's email 
    Mail::to($request->email)->send(new PasswordResetMail($resetUrl));

    return back()->with('success', 'A password reset link has been sent to your email address.');
}

public function showResetPass(Request $request)
{
    $token = $request->token;
    $email = $request->email;

    return view('authentication.resetpass', compact('token', 'email'));
}

public function resetPassword(Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
    }

    $reset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$reset) {
        return response()->json(['success' => false, 'error' => 'Invalid token or email.']);
    }

    // Check expiry (e.g., 1 hour)
    if (Carbon::parse($reset->created_at)->addHours(1)->isPast()) {
        return response()->json(['success' => false, 'error' => 'This password reset link has expired.']);
    }

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['success' => false, 'error' => 'User not found.']);
    }

    $user->password = Hash::make($request->password);
    $user->save();
// delete reset token
    DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

    return response()->json(['success' => true]);
}
   

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}