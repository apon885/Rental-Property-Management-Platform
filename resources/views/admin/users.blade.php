@extends('layouts.dashboard')
@section('title', 'Users')

@section('content')
<form method="GET" class="panel panel-body mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-lg-5"><label class="form-label" for="q">Search</label>
            <input id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, email or phone"></div>
        <div class="col-6 col-lg-2"><label class="form-label" for="role">Role</label>
            <select id="role" name="role" class="form-select"><option value="">All</option>
                @foreach (['tenant', 'landlord', 'admin'] as $r)<option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>@endforeach
            </select></div>
        <div class="col-6 col-lg-2"><label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select"><option value="">All</option>
                @foreach (['active', 'suspended'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select></div>
        <div class="col-lg-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" aria-label="Clear filters"><i class="fa-solid fa-rotate-left"></i></a></div>
    </div>
</form>

<div class="panel">
    @if ($users->isEmpty())
        <div class="empty"><i class="fa-solid fa-users"></i><strong>No users found</strong>Try a different search.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>User</th><th>Role</th><th>Phone</th><th>Identity</th><th>Account</th><th>Joined</th><th></th></tr></thead>
                <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}<span class="sub">{{ $u->email }}</span></td>
                        <td>{{ ucfirst($u->role) }}</td>
                        <td>{{ $u->phone ?: '-' }}</td>
                        <td><x-badge :status="$u->verification_status" /></td>
                        <td><x-badge :status="$u->status" /></td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td>
                            @unless ($u->isAdmin())
                                <div class="actions">
                                    <form method="POST" action="{{ route('admin.users.status', $u) }}">@csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary">{{ $u->status === 'active' ? 'Suspend' : 'Reactivate' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete {{ addslashes($u->name) }} and all of their data? This cannot be undone.')">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-secondary text-danger" aria-label="Delete {{ $u->name }}"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            @endunless
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
