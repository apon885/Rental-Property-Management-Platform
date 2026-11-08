@extends('layouts.dashboard')
@section('title', 'Bookings')

@section('content')
<ul class="nav mb-3 gap-1">
    @foreach (['' => 'All', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
        <li><a class="btn btn-sm {{ (string) request('status') === (string) $key ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ route('admin.bookings.index', $key ? ['status' => $key] : []) }}">{{ $label }}</a></li>
    @endforeach
</ul>

<div class="panel">
    @if ($bookings->isEmpty())
        <div class="empty"><i class="fa-solid fa-key"></i><strong>No bookings found</strong></div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Tenant</th><th>Landlord</th><th>Contract</th><th>Rent</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($bookings as $b)
                    <tr>
                        <td class="fw-semibold">{{ $b->property->title }}</td>
                        <td>{{ $b->tenant->name }}<span class="sub">{{ $b->tenant->phone }}</span></td>
                        <td>{{ $b->landlord->name }}<span class="sub">{{ $b->landlord->phone }}</span></td>
                        <td>{{ $b->start_date->format('d M Y') }}<span class="sub">to {{ $b->end_date->format('d M Y') }}</span></td>
                        <td>৳{{ number_format($b->monthly_rent) }}</td>
                        <td><x-badge :status="$b->status" /></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
