@extends('layouts.dashboard')
@section('title', 'Payments')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="stat-strip" style="min-width:280px"><div class="stat"><div class="stat-label">Total processed</div><div class="stat-value">৳{{ number_format($total) }}</div></div></div>
    <a href="{{ route('admin.payments.export') }}" class="btn btn-outline-primary"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a>
</div>

<div class="panel">
    @if ($payments->isEmpty())
        <div class="empty"><i class="fa-solid fa-wallet"></i><strong>No payments yet</strong></div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Paid on</th><th>Tenant</th><th>Property</th><th>Landlord</th><th>Month</th><th>Method</th><th>Transaction</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($payments as $p)
                    <tr>
                        <td class="text-nowrap">{{ optional($p->paid_at)->format('d M Y, h:i A') }}</td>
                        <td>{{ $p->tenant->name }}</td>
                        <td>{{ $p->booking->property->title }}</td>
                        <td>{{ $p->booking->landlord->name }}</td>
                        <td>{{ $p->month_label }}</td>
                        <td>{{ $p->method_label }}</td>
                        <td><code>{{ $p->transaction_id }}</code></td>
                        <td class="fw-semibold">৳{{ number_format($p->amount) }}</td>
                        <td><x-badge :status="$p->status" /></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $payments->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
