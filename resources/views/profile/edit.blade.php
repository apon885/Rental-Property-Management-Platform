@extends('layouts.dashboard')
@section('title', 'My account')

@section('content')
<div class="row g-4">
    <div class="col-xl-7">
        <div class="panel mb-4">
            <div class="panel-head"><h2>Account information</h2><x-badge :status="$user->role === 'admin' ? 'verified' : $user->status" /></div>
            <form method="POST" action="{{ route('profile.update') }}" class="panel-body">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">Mobile number</label>
                        <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" value="{{ $user->email }}" class="form-control" disabled>
                        <div class="form-text">Email is your sign-in name and cannot be changed here.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nid_number">National ID number</label>
                        <input id="nid_number" name="nid_number" value="{{ old('nid_number', $user->nid_number) }}" class="form-control @error('nid_number') is-invalid @enderror" inputmode="numeric">
                        @error('nid_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="address">Address</label>
                        <input id="address" name="address" value="{{ old('address', $user->address) }}" class="form-control @error('address') is-invalid @enderror">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button class="btn btn-primary mt-4">Save changes</button>
            </form>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Change password</h2></div>
            <form method="POST" action="{{ route('profile.password') }}" class="panel-body">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="current_password">Current password</label>
                        <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password">New password</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>
                <button class="btn btn-primary mt-4">Update password</button>
            </form>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel">
            <div class="panel-head"><h2>Identity verification</h2><x-badge :status="$user->verification_status" /></div>
            <div class="panel-body">
                @if ($user->isAdmin())
                    <p class="muted mb-0">Administrator accounts do not need verification.</p>
                @elseif ($user->verification_status === 'verified')
                    <p class="mb-0">Your identity is verified. Landlords and tenants see a verified badge next to your name.</p>
                @elseif ($user->verification_status === 'pending')
                    <p class="mb-0">An administrator is reviewing your details. You will see the result here.</p>
                @else
                    @if ($user->verification_status === 'rejected')
                        <div class="alert alert-danger">Your last request was rejected. Check your NID number and request again.</div>
                    @endif
                    <p class="muted">Add your NID number in your account information, then send it for review.</p>
                    <form method="POST" action="{{ route('profile.verify') }}">@csrf
                        <button class="btn btn-accent">Request verification</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
