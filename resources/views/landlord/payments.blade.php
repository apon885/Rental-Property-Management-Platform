@extends('layouts.dashboard')
@section('title', 'Rent payments')

@section('content')
<div class="stat-strip mb-4" style="max-width:420px">
    <div class="stat"><div class="stat-label">Rent received</div><div class="stat-value">৳{{ number_format($total) }}</div></div>
</div>

<div class="panel">
    @if ($payments->isEmpty())
        <div class="empty"><i class="fa-solid fa-coins"></i><strong>No payments yet</strong>Payments appear here as soon as a tenant pays.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Tenant</th><th>Property</th><th>Month</th><th>Method</th><th>Transaction</th><th>Paid on</th><th>Amount</th></tr></thead>
                <tbody>
                @foreach ($payments as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->tenant->name }}</td>
                        <td>{{ $p->booking->property->title }}</td>
                        <td>{{ $p->month_label }}</td>
                        <td>{{ $p->method_label }}</td>
                        <td><code>{{ $p->transaction_id }}</code></td>
                        <td>{{ optional($p->paid_at)->format('d M Y, h:i A') }}</td>
                        <td class="fw-semibold">৳{{ number_format($p->amount) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $payments->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
