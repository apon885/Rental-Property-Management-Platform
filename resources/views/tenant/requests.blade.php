@extends('layouts.dashboard')
@section('title', 'Rental requests')

@section('content')
<div class="panel">
    @if ($requests->isEmpty())
        <div class="empty"><i class="fa-regular fa-paper-plane"></i><strong>No rental requests yet</strong>
            <a href="{{ route('properties.index') }}">Find a property</a> and send your first request.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Landlord</th><th>Move-in</th><th>Stay</th><th>Sent</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($requests as $r)
                    <tr>
                        <td><a href="{{ route('properties.show', $r->property) }}" class="fw-semibold text-decoration-none">{{ $r->property->title }}</a>
                            <span class="sub">৳{{ number_format($r->property->rent) }} / month</span></td>
                        <td>{{ $r->property->landlord->name }}</td>
                        <td>{{ $r->move_in_date->format('d M Y') }}</td>
                        <td>{{ $r->duration_months }} mo</td>
                        <td>{{ $r->created_at->diffForHumans() }}</td>
                        <td><x-badge :status="$r->status" /></td>
                        <td class="text-end">
                            @if ($r->status === 'pending')
                                <form method="POST" action="{{ route('tenant.requests.cancel', $r) }}" onsubmit="return confirm('Cancel this request?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                                </form>
                            @elseif ($r->status === 'approved')
                                <a href="{{ route('tenant.bookings.index') }}" class="btn btn-sm btn-primary">View booking</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $requests->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
