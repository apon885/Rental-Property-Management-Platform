@extends('layouts.dashboard')
@section('title', 'Landlord overview')

@section('content')
@include('partials.stats')

@if (auth()->user()->verification_status !== 'verified')
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><i class="fa-solid fa-id-card me-2"></i>Verified landlords earn more trust from tenants.</div>
        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">Verify my identity</a>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-7">
        <div class="panel h-100">
            <div class="panel-head"><h2>Requests waiting for you</h2><a href="{{ route('landlord.requests.index') }}" class="btn btn-outline-secondary btn-sm">View all</a></div>
            @if ($requests->isEmpty())
                <div class="empty"><i class="fa-solid fa-inbox"></i><strong>Inbox is clear</strong>New tenant requests will show up here.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead><tr><th>Tenant</th><th>Property</th><th>Move-in</th><th></th></tr></thead>
                        <tbody>
                        @foreach ($requests as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->tenant->name }}<span class="sub">{{ $r->duration_months }} months</span></td>
                                <td>{{ $r->property->title }}</td>
                                <td>{{ $r->move_in_date->format('d M Y') }}</td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('landlord.requests.approve', $r) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-primary">Accept</button></form>
                                        <form method="POST" action="{{ route('landlord.requests.reject', $r) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary">Decline</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel h-100">
            <div class="panel-head"><h2>Latest rent payments</h2><a href="{{ route('landlord.payments.index') }}" class="btn btn-outline-secondary btn-sm">View all</a></div>
            @if ($payments->isEmpty())
                <div class="empty"><i class="fa-solid fa-coins"></i><strong>No payments yet</strong>Rent paid by your tenants appears here.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <tbody>
                        @foreach ($payments as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->tenant->name }}<span class="sub">{{ $p->month_label }}, {{ $p->booking->property->title }}</span></td>
                                <td class="text-end fw-semibold">৳{{ number_format($p->amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
