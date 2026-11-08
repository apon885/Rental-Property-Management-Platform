@extends('layouts.app')
@section('title', $property->title)

@section('content')
<div class="container py-4">
    <nav class="mb-3 small"><a href="{{ route('properties.index') }}"><i class="fa-solid fa-arrow-left me-1"></i>Back to results</a></nav>

    @if ($property->status !== 'approved')
        <div class="alert alert-warning">This listing is <strong>{{ $property->status }}</strong> and is only visible to its owner and administrators.</div>
    @elseif (! $property->is_available)
        <div class="alert alert-warning">This property is currently not available for new requests.</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <img class="gallery mb-4" src="{{ $property->image_url }}" alt="{{ $property->title }}">
            <h1 class="h2 mb-1">{{ $property->title }}</h1>
            <div class="muted mb-3"><i class="fa-solid fa-location-dot me-1"></i>{{ $property->address }}, {{ $property->area }}, {{ $property->city }}</div>

            <dl class="facts mb-4">
                <div><dt>Type</dt><dd>{{ $property->type_label }}</dd></div>
                <div><dt>Bedrooms</dt><dd>{{ $property->bedrooms }}</dd></div>
                <div><dt>Bathrooms</dt><dd>{{ $property->bathrooms }}</dd></div>
                <div><dt>Size</dt><dd>{{ $property->size_sqft ? number_format($property->size_sqft) . ' sqft' : 'Not listed' }}</dd></div>
                <div><dt>Available from</dt><dd>{{ $property->available_from ? $property->available_from->format('d M Y') : 'Now' }}</dd></div>
            </dl>

            <h2 class="h4">About this property</h2>
            <p style="white-space:pre-line">{{ $property->description }}</p>

            @if (count($property->amenity_list))
                <h2 class="h4 mt-4">Amenities</h2>
                <div>@foreach ($property->amenity_list as $a)<span class="chip">{{ $a }}</span>@endforeach</div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="panel panel-body position-sticky" style="top:1rem">
                <div class="pcard-rent" style="font-size:1.8rem">৳{{ number_format($property->rent) }} <small>/ month</small></div>
                <div class="muted mb-3">Security deposit ৳{{ number_format($property->deposit) }}</div>

                <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <i class="fa-solid fa-circle-user fs-3 text-secondary"></i>
                    <div>
                        <div class="fw-semibold">{{ $property->landlord->name }}</div>
                        <x-badge :status="$property->landlord->verification_status" />
                    </div>
                </div>

                @guest
                    <p class="muted">Sign in as a tenant to send a rental request.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Sign in to request</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100">Create a tenant account</a>
                @endguest

                @auth
                    @if (auth()->user()->isTenant())
                        @if ($pendingRequest)
                            <div class="alert alert-success mb-0">You have a pending request for this property. <a href="{{ route('tenant.requests.index') }}">View requests</a></div>
                        @elseif ($property->status === 'approved' && $property->is_available)
                            <form method="POST" action="{{ route('tenant.requests.store', $property) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="move_in_date">Move-in date</label>
                                    <input id="move_in_date" type="date" name="move_in_date" min="{{ now()->toDateString() }}" value="{{ old('move_in_date') }}" class="form-control @error('move_in_date') is-invalid @enderror" required>
                                    @error('move_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="duration_months">Stay length (months)</label>
                                    <input id="duration_months" type="number" min="1" max="36" name="duration_months" value="{{ old('duration_months', 12) }}" class="form-control @error('duration_months') is-invalid @enderror" required>
                                    @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="message">Message to landlord <span class="muted fw-normal">(optional)</span></label>
                                    <textarea id="message" name="message" rows="3" maxlength="500" class="form-control @error('message') is-invalid @enderror" placeholder="Family size, work, questions about the flat">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button class="btn btn-accent w-100 btn-lg">Send rental request</button>
                            </form>
                        @endif
                    @elseif (auth()->id() === $property->landlord_id)
                        <a href="{{ route('landlord.properties.edit', $property) }}" class="btn btn-primary w-100">Edit this listing</a>
                    @else
                        <p class="muted mb-0">Only tenant accounts can send rental requests.</p>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @if ($similar->isNotEmpty())
        <h2 class="h4 mt-5 mb-3">Similar rentals</h2>
        <div class="row g-4">
            @foreach ($similar as $p)<div class="col-md-6 col-lg-4"><x-property-card :property="$p" /></div>@endforeach
        </div>
    @endif
</div>
@endsection
