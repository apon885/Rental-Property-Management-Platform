@extends('layouts.dashboard')
@section('title', 'Admin overview')

@section('content')
@include('partials.stats')

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="panel h-100">
            <div class="panel-head"><h2>Listings to review</h2><a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-secondary btn-sm">Open queue</a></div>
            @if ($pendingProperties->isEmpty())
                <div class="empty"><i class="fa-solid fa-circle-check"></i><strong>Nothing waiting</strong>New listings will show up here.</div>
            @else
                <div class="table-wrap"><table class="data-table"><tbody>
                @foreach ($pendingProperties as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->title }}<span class="sub">by {{ $p->landlord->name }}</span></td>
                        <td class="text-end">
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-primary">Approve</button></form>
                                <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-secondary">Reject</button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody></table></div>
            @endif
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel h-100">
            <div class="panel-head"><h2>Identity checks</h2><a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-secondary btn-sm">Open queue</a></div>
            @if ($pendingUsers->isEmpty())
                <div class="empty"><i class="fa-solid fa-circle-check"></i><strong>Nothing waiting</strong>Verification requests will show up here.</div>
            @else
                <div class="table-wrap"><table class="data-table"><tbody>
                @foreach ($pendingUsers as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}<span class="sub">{{ ucfirst($u->role) }}, NID {{ $u->nid_number }}</span></td>
                        <td class="text-end">
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.users.verify', $u) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-primary">Verify</button></form>
                                <form method="POST" action="{{ route('admin.users.verify', $u) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-secondary">Reject</button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody></table></div>
            @endif
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><h2>Recent activity</h2><a href="{{ route('admin.security.index') }}" class="btn btn-outline-secondary btn-sm">Security log</a></div>
    @include('admin._logs', ['logs' => $logs])
</div>
@endsection
