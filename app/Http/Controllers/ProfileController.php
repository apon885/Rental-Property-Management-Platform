<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'phone'      => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'address'    => ['nullable', 'string', 'max:255'],
            'nid_number' => ['nullable', 'regex:/^\d{10,17}$/'],
        ], [
            'phone.regex'      => 'Enter a Bangladeshi mobile number, for example 017XXXXXXXX.',
            'nid_number.regex' => 'NID must be 10 to 17 digits.',
        ]);

        // Changing the NID after approval needs a fresh review.
        if (($data['nid_number'] ?? null) !== $user->nid_number && $user->verification_status === 'verified') {
            $data['verification_status'] = 'unverified';
        }

        $user->update($data);
        ActivityLog::record('profile_update', 'Updated account information.');

        return back()->with('success', 'Account information saved.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers(), 'different:current_password'],
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);
        ActivityLog::record('password_change', 'Changed password.');

        return back()->with('success', 'Password updated.');
    }

    public function requestVerification(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            abort(403);
        }

        if (! $user->nid_number) {
            return back()->with('error', 'Add your NID number first, then request verification.');
        }

        if (in_array($user->verification_status, ['unverified', 'rejected'], true)) {
            $user->update(['verification_status' => 'pending']);
            ActivityLog::record('verification_request', 'Requested identity verification.');
        }

        return back()->with('success', 'Verification requested. An admin will review it soon.');
    }
}
