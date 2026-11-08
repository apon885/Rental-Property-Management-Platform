@extends('layouts.dashboard')
@section('title', 'Properties')

@section('content')
<form method="GET" class="panel panel-body mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-lg-6"><label class="form-label" for="q">Search title</label><input id="q" name="q" value="{{ request('q') }}" class="form-control"></div>
        <div class="col-6 col-lg-3"><label class="form-label" for="status">Review status</label>
            <select id="status" name="status" class="form-select"><option value="">All</option>
                @foreach (['pending', 'approved', 'rejected'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select></div>
        <div class="col-6 col-lg-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary" aria-label="Clear filters"><i class="fa-solid fa-rotate-left"></i></a></div>
    </div>
</form>

<div class="panel">
    @if ($properties->isEmpty())
        <div class="empty"><i class="fa-regular fa-building"></i><strong>No properties found</strong></div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Landlord</th><th>Rent</th><th>Review</th><th>Availability</th><th></th></tr></thead>
                <tbody>
                @foreach ($properties as $p)
                    <tr>
                        <td><div class="d-flex align-items-center gap-3"><img class="thumb" src="{{ $p->image_url }}" alt="">
                            <div><a href="{{ route('properties.show', $p) }}" class="fw-semibold text-decoration-none">{{ $p->title }}</a><span class="sub">{{ $p->area }}, {{ $p->type_label }}</span></div></div></td>
                        <td>{{ $p->landlord->name }}</td>
                        <td>৳{{ number_format($p->rent) }}</td>
                        <td><x-badge :status="$p->status" /></td>
                        <td><x-badge :status="$p->is_available ? 'available' : 'rented'" /></td>
                        <td>
                            <div class="actions">
                                @if ($p->status !== 'approved')
                                    <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-primary">Approve</button></form>
                                @endif
                                @if ($p->status !== 'rejected')
                                    <form method="POST" action="{{ route('admin.properties.review', $p) }}">@csrf @method('PATCH')<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-secondary">Reject</button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.properties.destroy', $p) }}" onsubmit="return confirm('Delete this listing permanently?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary text-danger" aria-label="Delete {{ $p->title }}"><i class="fa-solid fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $properties->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
