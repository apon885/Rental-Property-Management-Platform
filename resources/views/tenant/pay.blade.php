@extends('layouts.dashboard')
@section('title', 'Pay rent')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('tenant.payments.store', $booking) }}" class="panel">
            @csrf
            <div class="panel-head"><h2>Payment details</h2></div>
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label" for="billing_month">Month to pay</label>
                    <select id="billing_month" name="billing_month" class="form-select @error('billing_month') is-invalid @enderror" required>
                        @foreach ($months as $m)
                            <option value="{{ $m }}" @selected(old('billing_month') === $m)>{{ \Carbon\Carbon::parse($m . '-01')->format('F Y') }}</option>
                        @endforeach
                    </select>
                    @error('billing_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="method">Payment method</label>
                    <select id="method" name="method" class="form-select @error('method') is-invalid @enderror" required>
                        @foreach (\App\Models\Payment::METHODS as $k => $v)
                            <option value="{{ $k }}" @selected(old('method') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="transaction_id">Transaction ID</label>
                    <input id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="e.g. 9K4L2M7QX" required>
                    @error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Enter the reference from your bKash, Nagad, Rocket, card or bank receipt. This is a demo gateway, so any unique 6-40 character code works.</div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Pay ৳{{ number_format($booking->monthly_rent) }}</button>
                    <a href="{{ route('tenant.bookings.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-head"><h2>Summary</h2></div>
            <div class="panel-body">
                <div class="fw-semibold">{{ $booking->property->title }}</div>
                <div class="muted mb-3">{{ $booking->property->address }}</div>
                <dl class="facts" style="grid-template-columns:1fr 1fr">
                    <div><dt>Monthly rent</dt><dd>৳{{ number_format($booking->monthly_rent) }}</dd></div>
                    <div><dt>Months left to pay</dt><dd>{{ $months->count() }}</dd></div>
                    <div><dt>Contract starts</dt><dd>{{ $booking->start_date->format('d M Y') }}</dd></div>
                    <div><dt>Contract ends</dt><dd>{{ $booking->end_date->format('d M Y') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
