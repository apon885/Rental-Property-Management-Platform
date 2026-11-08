@extends('layouts.app')
@section('title', 'Rent a verified home')

@section('content')
<section class="hero">
    <div class="container position-relative" style="z-index:1">
        <h1>Rent a verified home, straight from the landlord.</h1>
        <p class="lead mt-3">Browse listings that our team has reviewed, send a rental request, and pay your rent online.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="{{ route('properties.index') }}" class="btn btn-accent btn-lg">Browse rentals</a>
            @guest<a href="{{ route('register', ['role' => 'landlord']) }}" class="btn btn-outline-light btn-lg">List your property</a>@endguest
        </div>
    </div>
</section>

<div class="container">
    <form class="search-sheet" method="GET" action="{{ route('properties.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-lg-4">
                <label class="form-label" for="hq">Area or keyword</label>
                <input id="hq" list="areaList" name="q" class="form-control" placeholder="Mirpur, Dhanmondi, near metro…">
                <datalist id="areaList">@foreach ($areas as $a)<option value="{{ $a }}">@endforeach</datalist>
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="ht">Property type</label>
                <select id="ht" name="type" class="form-select">
                    <option value="">Any type</option>
                    @foreach (\App\Models\Property::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="hm">Max rent (৳ / month)</label>
                <input id="hm" type="number" min="0" step="500" name="max_rent" class="form-control" placeholder="30000">
            </div>
            <div class="col-lg-2 d-grid"><button class="btn btn-primary">Search rentals</button></div>
        </div>
    </form>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Newest rentals</h2>
                <div class="muted">{{ $counts['listings'] }} approved listings are open right now.</div>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary btn-sm">See all</a>
        </div>

        @if ($featured->isEmpty())
            <div class="panel empty"><i class="fa-regular fa-building"></i><strong>No listings yet</strong>Approved properties will appear here.</div>
        @else
            <div class="row g-4">
                @foreach ($featured as $property)
                    <div class="col-md-6 col-lg-4"><x-property-card :property="$property" /></div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="audience">
                    <h3>For tenants</h3>
                    <p class="muted mb-0">Search by area, price and bedrooms. Send a request with your move-in date, then manage your booking and pay rent from one place.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="audience">
                    <h3>For landlords</h3>
                    <p class="muted mb-0">Publish your property, control when it is available, review tenant requests and see every rent payment as it arrives.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
