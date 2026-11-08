@extends('layouts.dashboard')
@section('title', 'Reports')

@section('content')
@include('partials.stats', ['stats' => [
    ['label' => 'Users', 'value' => $summary['users']],
    ['label' => 'Listings', 'value' => $summary['properties'], 'hint' => $summary['listed'] . ' open now'],
    ['label' => 'Request approval rate', 'value' => $summary['requests'] ? round($summary['approved'] / $summary['requests'] * 100) . '%' : '0%', 'hint' => $summary['approved'] . ' of ' . $summary['requests'] . ' requests'],
    ['label' => 'Occupancy', 'value' => $summary['occupancy'] . '%', 'hint' => $summary['active_bookings'] . ' active bookings'],
    ['label' => 'Revenue processed', 'value' => '৳' . number_format($summary['revenue'])],
]])

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="panel h-100">
            <div class="panel-head"><h2>Rent collected, last 6 months</h2><a href="{{ route('admin.payments.export') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a></div>
            <div class="panel-body"><canvas id="revenueChart" height="110" aria-label="Monthly rent collected" role="img"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="panel h-100">
            <div class="panel-head"><h2>Listings by type</h2></div>
            <div class="panel-body">
                @if ($byType->isEmpty())
                    <div class="empty"><strong>No listings yet</strong></div>
                @else
                    <canvas id="typeChart" height="200" aria-label="Listings by type" role="img"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><h2>Busiest areas</h2></div>
    @if ($topAreas->isEmpty())
        <div class="empty"><strong>No approved listings yet</strong></div>
    @else
        <div class="table-wrap"><table class="data-table">
            <thead><tr><th>Area</th><th>Approved listings</th><th>Average rent</th></tr></thead>
            <tbody>@foreach ($topAreas as $a)<tr><td class="fw-semibold">{{ $a->area }}</td><td>{{ $a->total }}</td><td>৳{{ number_format($a->avg_rent) }}</td></tr>@endforeach</tbody>
        </table></div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    var revenue = @json($revenue);
    var types = @json($byType);
    var typeNames = @json(\App\Models\Property::TYPES);

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: { labels: revenue.labels, datasets: [{ label: 'Rent collected (৳)', data: revenue.values, backgroundColor: '#0F3D3E', borderRadius: 6 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#E6ECEA' } }, x: { grid: { display: false } } } }
    });

    var typeCanvas = document.getElementById('typeChart');
    if (typeCanvas) {
        var keys = Object.keys(types);
        new Chart(typeCanvas, {
            type: 'doughnut',
            data: { labels: keys.map(function (k) { return typeNames[k] || k; }), datasets: [{ data: keys.map(function (k) { return types[k]; }), backgroundColor: ['#0F3D3E', '#C8963E', '#5B8E7D', '#245E8A', '#A93226'] }] },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>
@endpush
