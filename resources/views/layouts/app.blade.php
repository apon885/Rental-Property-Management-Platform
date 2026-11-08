<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Find a rental home') · {{ config('app.name') }}</title>
    @include('partials.head')
</head>
<body>
<nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
        <a class="navbar-brand brand" href="{{ route('home') }}">
            <span class="brand-mark"><i class="fa-solid fa-house-chimney"></i></span>{{ config('app.name') }}
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav me-auto ms-lg-4">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index') }}">Browse rentals</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center mt-3 mt-lg-0">
                @auth
                    <a class="btn btn-primary btn-sm" href="{{ route('dashboard') }}">Open dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="btn btn-outline-secondary btn-sm">Sign out</button>
                    </form>
                @else
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Sign in</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Create account</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main>
    @hasSection('flash')
    @else
        @if (session('success') || session('error'))
            <div class="container pt-3">@include('partials.flash')</div>
        @endif
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <div>© {{ date('Y') }} {{ config('app.name') }}. All listings are reviewed before they go live.</div>
        <div><a href="{{ route('properties.index') }}">Browse rentals</a></div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
