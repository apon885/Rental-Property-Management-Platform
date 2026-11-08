@extends('layouts.dashboard')
@section('title', 'Welcome back, ' . \Illuminate\Support\Str::before(auth()->user()->name, ' '))

@section('content')
@include('partials.stats')

<form method="GET" action="{{ route('properties.index') }}" class="panel panel-body mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-lg-5">
            <label class="form-label" for="q">Search properties</label>
            <input id="q" name="q" class="form-control" placeholder="Area, street or keyword">
        </div>
        <div class="col-6 col-lg-3">
            <label class="form-label" for="type">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">Any type</option>
                @foreach (\App\Models\Property::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label class="form-label" for="max_rent">Max rent (৳)</label>
            <input id="max_rent" type="number" min="0" name="max_rent" class="form-control">
        </div>
        <div class="col-lg-2 d-grid"><button class="btn btn-primary">Search</button></div>
    </div>
</form>

@if ($dueBookings->isNotEmpty())
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><i class="fa-solid fa-circle-exclamation me-2"></i>Rent is due for {{ $dueBookings->count() }} {{ \Illuminate\Support\Str::plural('booking', $dueBookings->count()) }}.</div>
        <a class="btn btn-sm btn-primary" href="{{ route('tenant.payments.create', $dueBookings->first()) }}">Pay now</a>
    </div>
@endif

<div class="panel mb-4">
    <div class="panel-head">
        <div>
            <h2>Recommended for you</h2>
            <div class="muted small">{{ $reason }}</div>
        </div>
        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary btn-sm">Browse all</a>
    </div>
    <div class="panel-body">
        @if ($recommended->isEmpty())
            <div class="empty"><i class="fa-regular fa-building"></i><strong>Nothing to recommend yet</strong>Approved listings will show up here.</div>
        @else
            <div class="row g-4">
                @foreach ($recommended as $p)<div class="col-md-6 col-xl-4"><x-property-card :property="$p" /></div>@endforeach
            </div>
        @endif
    </div>
</div>

<div class="panel">
    <div class="panel-head"><h2>Recent requests</h2><a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.requests.index') }}">View all</a></div>
    @if ($recentRequests->isEmpty())
        <div class="empty"><i class="fa-regular fa-paper-plane"></i><strong>No requests yet</strong>Open a property and send your first rental request.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Move-in</th><th>Stay</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($recentRequests as $r)
                    <tr>
                        <td><a href="{{ route('properties.show', $r->property) }}" class="fw-semibold text-decoration-none">{{ $r->property->title }}</a><span class="sub">{{ $r->property->area }}</span></td>
                        <td>{{ $r->move_in_date->format('d M Y') }}</td>
                        <td>{{ $r->duration_months }} mo</td>
                        <td><x-badge :status="$r->status" /></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
