@extends('layouts.dashboard')
@section('title', 'Security')

@section('content')
@include('partials.stats', ['stats' => [
    ['label' => 'Sign-ins (24h)', 'value' => $summary['logins_24h']],
    ['label' => 'Failed attempts (24h)', 'value' => $summary['failed_24h'], 'hint' => $summary['failed_24h'] > 10 ? 'Higher than usual' : 'Looks normal'],
    ['label' => 'Blocked sign-ins (24h)', 'value' => $summary['blocked_24h']],
    ['label' => 'Suspended accounts', 'value' => $summary['suspended']],
]])

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-head"><h2>IPs with failed sign-ins (24h)</h2></div>
            @if ($suspiciousIps->isEmpty())
                <div class="empty"><i class="fa-solid fa-shield-halved"></i><strong>No failed attempts</strong>Nothing to review.</div>
            @else
                <div class="table-wrap"><table class="data-table"><thead><tr><th>IP address</th><th>Attempts</th></tr></thead><tbody>
                    @foreach ($suspiciousIps as $row)<tr><td><code>{{ $row->ip_address }}</code></td><td>{{ $row->attempts }}</td></tr>@endforeach
                </tbody></table></div>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-head"><h2>Suspended accounts</h2><a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" class="btn btn-outline-secondary btn-sm">Manage</a></div>
            @if ($suspended->isEmpty())
                <div class="empty"><i class="fa-solid fa-user-check"></i><strong>No suspended accounts</strong></div>
            @else
                <div class="table-wrap"><table class="data-table"><tbody>
                    @foreach ($suspended as $u)<tr><td class="fw-semibold">{{ $u->name }}<span class="sub">{{ $u->email }}</span></td><td>{{ ucfirst($u->role) }}</td></tr>@endforeach
                </tbody></table></div>
            @endif
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Activity log</h2>
        <form method="GET" class="d-flex gap-2">
            <select name="action" class="form-select form-select-sm" aria-label="Filter by action">
                <option value="">All actions</option>
                @foreach ($actions as $a)<option value="{{ $a }}" @selected(request('action') === $a)>{{ str_replace('_', ' ', $a) }}</option>@endforeach
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
    @include('admin._logs', ['logs' => $logs])
    <div class="p-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
