@extends('layouts.dashboard')
@section('title', 'Rental requests')

@section('content')
<ul class="nav nav-pills mb-3 gap-1">
    @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
        <li class="nav-item">
            <a class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ route('landlord.requests.index', ['status' => $key]) }}">{{ $label }}</a>
        </li>
    @endforeach
</ul>

<div class="panel">
    @if ($requests->isEmpty())
        <div class="empty"><i class="fa-solid fa-inbox"></i><strong>No {{ $status === 'all' ? '' : $status }} requests</strong>Tenant requests for your properties appear here.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Tenant</th><th>Property</th><th>Move-in</th><th>Stay</th><th>Message</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($requests as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r->tenant->name }}
                            <span class="sub">{{ $r->tenant->phone }}</span>
                            <x-badge :status="$r->tenant->verification_status" /></td>
                        <td>{{ $r->property->title }}<span class="sub">৳{{ number_format($r->property->rent) }} / month</span></td>
                        <td>{{ $r->move_in_date->format('d M Y') }}</td>
                        <td>{{ $r->duration_months }} mo</td>
                        <td style="max-width:240px">{{ $r->message ?: '-' }}</td>
                        <td><x-badge :status="$r->status" /></td>
                        <td>
                            @if ($r->status === 'pending')
                                <div class="actions">
                                    <form method="POST" action="{{ route('landlord.requests.approve', $r) }}" onsubmit="return confirm('Accept this tenant? Other pending requests for this property will be declined.')">@csrf @method('PATCH')<button class="btn btn-sm btn-primary">Accept</button></form>
                                    <form method="POST" action="{{ route('landlord.requests.reject', $r) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary">Decline</button></form>
                                </div>
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
