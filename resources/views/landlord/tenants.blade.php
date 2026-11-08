@extends('layouts.dashboard')
@section('title', 'Tenants')

@section('content')
<div class="panel">
    @if ($bookings->isEmpty())
        <div class="empty"><i class="fa-solid fa-users"></i><strong>No tenants yet</strong>When you accept a rental request, the tenant shows up here.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Tenant</th><th>Property</th><th>Contract</th><th>Rent</th><th>Months paid</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($bookings as $b)
                    <tr>
                        <td class="fw-semibold">{{ $b->tenant->name }}<span class="sub">{{ $b->tenant->phone }}</span></td>
                        <td>{{ $b->property->title }}</td>
                        <td>{{ $b->start_date->format('d M Y') }}<span class="sub">to {{ $b->end_date->format('d M Y') }}</span></td>
                        <td>৳{{ number_format($b->monthly_rent) }}</td>
                        <td>{{ $b->payments_count }} of {{ $b->duration_months }}</td>
                        <td><x-badge :status="$b->status" /></td>
                        <td class="text-end">
                            @if ($b->status === 'active')
                                <form method="POST" action="{{ route('landlord.bookings.end', $b) }}" onsubmit="return confirm('End this tenancy and make the property available again?')">@csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-secondary">End tenancy</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
