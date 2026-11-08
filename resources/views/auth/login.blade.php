<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · {{ config('app.name') }}</title>
    @include('partials.head')
</head>
<body>
<div class="auth-wrap">
    <aside class="auth-side">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark" style="background:var(--brass);color:var(--pine-deep)"><i class="fa-solid fa-house-chimney"></i></span>{{ config('app.name') }}</a>
        <div>
            <h1>Your rentals, requests and rent in one place.</h1>
            <p class="mt-3">Sign in to manage your account. Tenants, landlords and administrators each land on their own dashboard.</p>
        </div>
        <div></div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <h2 class="mb-1">Sign in</h2>
            <p class="muted mb-4">New here? <a href="{{ route('register') }}">Create an account</a></p>

            @include('partials.flash')

            <form method="POST" action="{{ url('/login') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" autofocus required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Keep me signed in on this device</label>
                </div>
                <button class="btn btn-primary w-100 btn-lg">Sign in</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
