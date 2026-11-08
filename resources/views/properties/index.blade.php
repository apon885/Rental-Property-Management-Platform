@extends('layouts.app')
@section('title', 'Browse rentals')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <h1 class="h2 mb-1">Browse rentals</h1>
        <div class="muted">{{ $properties->total() }} {{ \Illuminate\Support\Str::plural('property', $properties->total()) }} match your search.</div>
    </div>

    <form method="GET" action="{{ route('properties.index') }}" class="panel panel-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label" for="q">Keyword</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Title, address, area">
            </div>
            <div class="col-6 col-lg-2">
                <label class="form-label" for="area">Area</label>
                <input id="area" list="areas" name="area" value="{{ $filters['area'] ?? '' }}" class="form-control">
                <datalist id="areas">@foreach ($areas as $a)<option value="{{ $a }}">@endforeach</datalist>
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="">Any type</option>
                    @foreach (\App\Models\Property::TYPES as $k => $v)
                        <option value="{{ $k }}" @selected(($filters['type'] ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="bedrooms">Bedrooms</label>
                <select id="bedrooms" name="bedrooms" class="form-select">
                    <option value="">Any</option>
                    @foreach ([1, 2, 3, 4, 5] as $b)
                        <option value="{{ $b }}" @selected(($filters['bedrooms'] ?? '') == $b)>{{ $b }}+ beds</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="min_rent">Min rent (৳)</label>
                <input id="min_rent" type="number" min="0" name="min_rent" value="{{ $filters['min_rent'] ?? '' }}" class="form-control">
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="max_rent">Max rent (৳)</label>
                <input id="max_rent" type="number" min="0" name="max_rent" value="{{ $filters['max_rent'] ?? '' }}" class="form-control">
            </div>
            <div class="col-6 col-lg-3">
                <label class="form-label" for="sort">Sort by</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Newest first</option>
                    <option value="rent_asc" @selected(($filters['sort'] ?? '') === 'rent_asc')>Rent: low to high</option>
                    <option value="rent_desc" @selected(($filters['sort'] ?? '') === 'rent_desc')>Rent: high to low</option>
                </select>
            </div>
            <div class="col-6 col-lg-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Apply filters</button>
                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary" title="Clear filters" aria-label="Clear filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </div>
    </form>

    @if ($properties->isEmpty())
        <div class="panel empty">
            <i class="fa-solid fa-magnifying-glass"></i>
            <strong>No properties match these filters</strong>
            Try a wider rent range or remove the area filter.
        </div>
    @else
        <div class="row g-4">
            @foreach ($properties as $property)
                <div class="col-md-6 col-lg-4"><x-property-card :property="$property" /></div>
            @endforeach
        </div>
        <div class="mt-4">{{ $properties->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
