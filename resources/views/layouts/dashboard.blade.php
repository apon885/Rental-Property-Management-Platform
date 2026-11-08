@php
    $user = auth()->user();
    $role = $user->role;
    $menus = [
        'tenant' => [
            ['route' => 'tenant.dashboard',      'pattern' => 'tenant.dashboard',    'icon' => 'fa-gauge-high',      'label' => 'Overview'],
            ['route' => 'properties.index',      'pattern' => 'properties.*',        'icon' => 'fa-magnifying-glass','label' => 'Find properties'],
            ['route' => 'tenant.requests.index', 'pattern' => 'tenant.requests.*',   'icon' => 'fa-paper-plane',     'label' => 'Rental requests'],
            ['route' => 'tenant.bookings.index', 'pattern' => 'tenant.bookings.*',   'icon' => 'fa-key',             'label' => 'My bookings'],
            ['route' => 'tenant.payments.index', 'pattern' => 'tenant.payments.*',   'icon' => 'fa-wallet',          'label' => 'Payments'],
        ],
        'landlord' => [
            ['route' => 'landlord.dashboard',      'pattern' => 'landlord.dashboard',    'icon' => 'fa-gauge-high', 'label' => 'Overview'],
            ['route' => 'landlord.properties.index','pattern' => 'landlord.properties.*', 'icon' => 'fa-building',    'label' => 'My listings'],
            ['route' => 'landlord.requests.index', 'pattern' => 'landlord.requests.*',   'icon' => 'fa-inbox',      'label' => 'Rental requests'],
            ['route' => 'landlord.tenants.index',  'pattern' => 'landlord.tenants.*',    'icon' => 'fa-users',      'label' => 'Tenants'],
            ['route' => 'landlord.payments.index', 'pattern' => 'landlord.payments.*',   'icon' => 'fa-coins',      'label' => 'Rent payments'],
        ],
        'admin' => [
            ['route' => 'admin.dashboard',             'pattern' => 'admin.dashboard',        'icon' => 'fa-gauge-high',        'label' => 'Overview'],
            ['route' => 'admin.users.index',           'pattern' => 'admin.users.*',          'icon' => 'fa-users-gear',        'label' => 'Users'],
            ['route' => 'admin.properties.index',      'pattern' => 'admin.properties.*',     'icon' => 'fa-building',          'label' => 'Properties'],
            ['route' => 'admin.verifications.index',   'pattern' => 'admin.verifications.*',  'icon' => 'fa-user-check',        'label' => 'Verification'],
            ['route' => 'admin.bookings.index',        'pattern' => 'admin.bookings.*',       'icon' => 'fa-key',               'label' => 'Bookings'],
            ['route' => 'admin.payments.index',        'pattern' => 'admin.payments.*',       'icon' => 'fa-wallet',            'label' => 'Payments'],
            ['route' => 'admin.security.index',        'pattern' => 'admin.security.*',       'icon' => 'fa-shield-halved',     'label' => 'Security'],
            ['route' => 'admin.reports.index',         'pattern' => 'admin.reports.*',        'icon' => 'fa-chart-column',      'label' => 'Reports'],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    @include('partials.head')
</head>
<body class="dash">
<div class="sidebar-backdrop" id="backdrop"></div>

<aside class="sidebar" id="sidebar" aria-label="Main menu">
    <a class="sidebar-brand" href="{{ route('home') }}">
        <span class="brand-mark"><i class="fa-solid fa-house-chimney"></i></span>{{ config('app.name') }}
    </a>
    <div class="sidebar-role">{{ ucfirst($role) }} workspace</div>

    <nav class="sidebar-nav">
        @foreach ($menus[$role] as $item)
            <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                <i class="fa-solid {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-foot">
        <div class="who">{{ $user->name }}</div>
        <div class="mail">{{ $user->email }}</div>
        <div class="d-flex gap-2">
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light flex-fill">Account</a>
            <form method="POST" action="{{ route('logout') }}" class="flex-fill">@csrf
                <button class="btn btn-sm btn-outline-light w-100">Sign out</button>
            </form>
        </div>
    </div>
</aside>

<div class="dash-main">
    <header class="topbar">
        <button class="btn btn-outline-secondary btn-sm d-lg-none" id="menuBtn" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <h1>@yield('title', 'Dashboard')</h1>
        <div class="ms-auto d-flex align-items-center gap-2">
            <x-badge :status="$user->verification_status" />
        </div>
    </header>

    <div class="dash-content">
        @include('partials.flash')
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var sidebar = document.getElementById('sidebar'), backdrop = document.getElementById('backdrop');
        function toggle(open) { sidebar.classList.toggle('open', open); backdrop.classList.toggle('show', open); }
        document.getElementById('menuBtn').addEventListener('click', function () { toggle(!sidebar.classList.contains('open')); });
        backdrop.addEventListener('click', function () { toggle(false); });
    })();
</script>
@stack('scripts')
</body>
</html>
