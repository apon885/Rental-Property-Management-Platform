<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $role = in_array($request->query('role'), ['tenant', 'landlord'], true) ? $request->query('role') : 'tenant';

        return view('auth.register', compact('role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email:rfc', 'max:150', 'unique:users,email'],
            'phone'    => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'unique:users,phone'],
            'role'     => ['required', 'in:tenant,landlord'],   // 'admin' is rejected here on purpose
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms'    => ['accepted'],
        ], [
            'phone.regex'    => 'Enter a Bangladeshi mobile number, for example 017XXXXXXXX.',
            'terms.accepted' => 'Please accept the terms to continue.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'role'     => $data['role'],
            'status'   => 'active',
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::record('register', "New {$user->role} account created.", $user->id);

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Welcome, ' . $user->name . '! Your account is ready.');
    }
}
