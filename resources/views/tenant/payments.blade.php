@extends('layouts.dashboard')
@section('title', 'Payments')

@section('content')
<div class="stat-strip mb-4" style="max-width:420px">
    <div class="stat"><div class="stat-label">Total paid</div><div class="stat-value">৳{{ number_format($total) }}</div></div>
</div>

<div class="panel">
    @if ($payments->isEmpty())
        <div class="empty"><i class="fa-solid fa-wallet"></i><strong>No payments yet</strong>Pay rent from the <a href="{{ route('tenant.bookings.index') }}">My bookings</a> page.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Property</th><th>Month</th><th>Method</th><th>Transaction</th><th>Paid on</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($payments as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->booking->property->title }}</td>
                        <td>{{ $p->month_label }}</td>
                        <td>{{ $p->method_label }}</td>
                        <td><code>{{ $p->transaction_id }}</code></td>
                        <td>{{ optional($p->paid_at)->format('d M Y, h:i A') }}</td>
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
