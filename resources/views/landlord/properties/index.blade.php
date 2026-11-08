@extends('layouts.dashboard')
@section('title', 'My listings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="muted">Manage your properties and decide when each one is open for requests.</div>
    <a href="{{ route('landlord.properties.create') }}" class="btn btn-accent"><i class="fa-solid fa-plus me-1"></i>Add property</a>
</div>

<div class="panel">
    @if ($properties->isEmpty())
        <div class="empty"><i class="fa-regular fa-building"></i><strong>You have not listed a property yet</strong>
            <a href="{{ route('landlord.properties.create') }}">Add your first property</a>. It goes live after an admin approves it.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Rent</th><th>Review</th><th>Availability</th><th>Requests</th><th></th></tr></thead>
                <tbody>
                @foreach ($properties as $p)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img class="thumb" src="{{ $p->image_url }}" alt="">
                                <div><a class="fw-semibold text-decoration-none" href="{{ route('properties.show', $p) }}">{{ $p->title }}</a>
                                    <span class="sub">{{ $p->area }}, {{ $p->type_label }}</span></div>
                            </div>
                        </td>
                        <td>৳{{ number_format($p->rent) }}</td>
                        <td><x-badge :status="$p->status" /></td>
                        <td>
                            <form method="POST" action="{{ route('landlord.properties.availability', $p) }}">@csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary" title="Toggle availability">
                                    @if ($p->is_available)<i class="fa-solid fa-toggle-on text-success me-1"></i>Available @else<i class="fa-solid fa-toggle-off me-1"></i>Not available @endif
                                </button>
                            </form>
                        </td>
                        <td>{{ $p->pending_requests_count }} pending</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('landlord.properties.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('landlord.properties.destroy', $p) }}" onsubmit="return confirm('Delete this listing permanently?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary text-danger" aria-label="Delete {{ $p->title }}"><i class="fa-solid fa-trash"></i></button>
                                </form>
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
