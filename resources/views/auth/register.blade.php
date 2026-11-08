<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account · {{ config('app.name') }}</title>
    @include('partials.head')
</head>
<body>
@php $selected = old('role', $role); @endphp
<div class="auth-wrap">
    <aside class="auth-side">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark" style="background:var(--brass);color:var(--pine-deep)"><i class="fa-solid fa-house-chimney"></i></span>{{ config('app.name') }}</a>
        <div>
            <h1>Find a home or fill one, without the middleman.</h1>
            <p class="mt-3">Create a free account as a tenant or a landlord. Administrator accounts are created by the platform team only.</p>
        </div>
        <div></div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <h2 class="mb-1">Create your account</h2>
            <p class="muted mb-4">Already registered? <a href="{{ route('login') }}">Sign in</a></p>

            <form method="POST" action="{{ url('/register') }}" novalidate>
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="role-option w-100 h-100">
                            <input type="radio" name="role" value="tenant" {{ $selected === 'tenant' ? 'checked' : '' }}>
                            <span class="role-box"><strong>Tenant</strong><span>I am looking for a place to rent</span></span>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="role-option w-100 h-100">
                            <input type="radio" name="role" value="landlord" {{ $selected === 'landlord' ? 'checked' : '' }}>
                            <span class="role-box"><strong>Landlord</strong><span>I want to list properties</span></span>
                        </label>
                    </div>
                    @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="name">Full name</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">Mobile number</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="017XXXXXXXX" autocomplete="tel" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">At least 8 characters with letters and numbers.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                    <label class="form-check-label" for="terms">I agree to the terms of use and privacy policy</label>
                    @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary w-100 btn-lg">Create account</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
