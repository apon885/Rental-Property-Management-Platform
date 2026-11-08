@extends('layouts.dashboard')
@section('title', 'My bookings')

@section('content')
<div class="panel">
    @if ($bookings->isEmpty())
        <div class="empty"><i class="fa-solid fa-key"></i><strong>No bookings yet</strong>A booking appears here when a landlord accepts your request.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Landlord contact</th><th>Period</th><th>Rent</th><th>Paid</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($bookings as $b)
                    @php $unpaid = $b->unpaidMonths()->count(); @endphp
                    <tr>
                        <td><a href="{{ route('properties.show', $b->property) }}" class="fw-semibold text-decoration-none">{{ $b->property->title }}</a>
                            <span class="sub">{{ $b->property->address }}</span></td>
                        <td>{{ $b->landlord->name }}<span class="sub">{{ $b->landlord->phone }}</span></td>
                        <td>{{ $b->start_date->format('d M Y') }}<span class="sub">to {{ $b->end_date->format('d M Y') }}</span></td>
                        <td>৳{{ number_format($b->monthly_rent) }}</td>
                        <td>{{ $b->duration_months - $unpaid }} of {{ $b->duration_months }} mo</td>
                        <td><x-badge :status="$b->status" /></td>
                        <td class="text-end">
                            @if ($b->status === 'active' && $unpaid > 0)
                                <a href="{{ route('tenant.payments.create', $b) }}" class="btn btn-sm btn-primary">Pay rent</a>
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
