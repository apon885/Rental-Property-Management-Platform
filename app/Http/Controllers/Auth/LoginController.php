<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status !== 'active' && Hash::check($credentials['password'], $user->password)) {
            ActivityLog::record('login_blocked', 'A suspended account tried to sign in.', $user->id);

            return back()->withErrors(['email' => 'Your account is suspended. Please contact support.'])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            ActivityLog::record('login_failed', 'Failed sign-in attempt for ' . $credentials['email'], $user?->id);

            return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        ActivityLog::record('login', 'Signed in.');

        return redirect()->route(Auth::user()->dashboardRoute());
    }

    public function destroy(Request $request)
    {
        ActivityLog::record('logout', 'Signed out.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
