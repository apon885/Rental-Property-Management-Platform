@extends('layouts.dashboard')
@section('title', 'Verification')

@section('content')
<div class="panel mb-4">
    <div class="panel-head"><h2>Identity requests</h2><span class="muted small">{{ $users->count() }} waiting</span></div>
    @if ($users->isEmpty())
        <div class="empty"><i class="fa-solid fa-circle-check"></i><strong>All caught up</strong>No identity requests are waiting.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>User</th><th>Role</th><th>NID number</th><th>Address</th><th></th></tr></thead>
                <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}<span class="sub">{{ $u->email }} / {{ $u->phone }}</span></td>
                        <td>{{ ucfirst($u->role) }}</td>
                        <td><code>{{ $u->nid_number }}</code></td>
                        <td>{{ $u->address ?: '-' }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.users.verify', $u) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-primary">Verify</button></form>
                                <form method="POST" action="{{ route('admin.users.verify', $u) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-secondary">Reject</button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="panel">
    <div class="panel-head"><h2>Property listings</h2><span class="muted small">{{ $properties->count() }} waiting</span></div>
    @if ($properties->isEmpty())
        <div class="empty"><i class="fa-solid fa-circle-check"></i><strong>All caught up</strong>No listings are waiting for review.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Landlord</th><th>Rent</th><th>Submitted</th><th></th></tr></thead>
                <tbody>
                @foreach ($properties as $p)
                    <tr>
                        <td><div class="d-flex align-items-center gap-3"><img class="thumb" src="{{ $p->image_url }}" alt="">
                            <div><a href="{{ route('properties.show', $p) }}" class="fw-semibold text-decoration-none">{{ $p->title }}</a><span class="sub">{{ $p->area }}, {{ $p->type_label }}</span></div></div></td>
                        <td>{{ $p->landlord->name }} <x-badge :status="$p->landlord->verification_status" /></td>
                        <td>৳{{ number_format($p->rent) }}</td>
                        <td>{{ $p->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-primary">Approve</button></form>
                                <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-secondary">Reject</button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
