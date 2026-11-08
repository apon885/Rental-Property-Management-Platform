@extends('layouts.dashboard')
@php $editing = $property->exists; @endphp
@section('title', $editing ? 'Edit listing' : 'Add property')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('landlord.properties.update', $property) : route('landlord.properties.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="panel mb-4">
                <div class="panel-head"><h2>Property details</h2>@if ($editing)<x-badge :status="$property->status" />@endif</div>
                <div class="panel-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="title">Listing title</label>
                            <input id="title" name="title" value="{{ old('title', $property->title) }}" class="form-control @error('title') is-invalid @enderror" placeholder="Sunlit 3-bed flat near Mirpur 10" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="type">Type</label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach (\App\Models\Property::TYPES as $k => $v)
                                    <option value="{{ $k }}" @selected(old('type', $property->type) === $k)>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bedrooms">Bedrooms</label>
                            <input id="bedrooms" type="number" min="0" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" class="form-control @error('bedrooms') is-invalid @enderror" required>
                            @error('bedrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bathrooms">Bathrooms</label>
                            <input id="bathrooms" type="number" min="0" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" class="form-control @error('bathrooms') is-invalid @enderror" required>
                            @error('bathrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="area">Area / locality</label>
                            <input id="area" name="area" value="{{ old('area', $property->area) }}" class="form-control @error('area') is-invalid @enderror" placeholder="Mirpur" required>
                            @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input id="city" name="city" value="{{ old('city', $property->city) }}" class="form-control @error('city') is-invalid @enderror" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="address">Full address</label>
                            <input id="address" name="address" value="{{ old('address', $property->address) }}" class="form-control @error('address') is-invalid @enderror" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $property->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="amenities">Amenities</label>
                            <input id="amenities" name="amenities" value="{{ old('amenities', $property->amenities) }}" class="form-control @error('amenities') is-invalid @enderror" placeholder="Lift, Generator, Gas, Parking">
                            <div class="form-text">Separate with commas.</div>
                            @error('amenities')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel mb-4">
                <div class="panel-head"><h2>Price and availability</h2></div>
                <div class="panel-body">
                    <div class="mb-3">
                        <label class="form-label" for="rent">Monthly rent (৳)</label>
                        <input id="rent" type="number" min="500" step="100" name="rent" value="{{ old('rent', $property->rent ? (int) $property->rent : '') }}" class="form-control @error('rent') is-invalid @enderror" required>
                        @error('rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="deposit">Security deposit (৳)</label>
                        <input id="deposit" type="number" min="0" step="100" name="deposit" value="{{ old('deposit', $property->deposit ? (int) $property->deposit : '') }}" class="form-control @error('deposit') is-invalid @enderror">
                        @error('deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="size_sqft">Size (sqft)</label>
                        <input id="size_sqft" type="number" min="50" name="size_sqft" value="{{ old('size_sqft', $property->size_sqft) }}" class="form-control @error('size_sqft') is-invalid @enderror">
                        @error('size_sqft')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="available_from">Available from</label>
                        <input id="available_from" type="date" name="available_from" value="{{ old('available_from', optional($property->available_from)->toDateString()) }}" class="form-control @error('available_from') is-invalid @enderror">
                        @error('available_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="panel mb-4">
                <div class="panel-head"><h2>Photo</h2></div>
                <div class="panel-body">
                    @if ($editing)<img src="{{ $property->image_url }}" alt="" class="gallery mb-3">@endif
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="form-control @error('image') is-invalid @enderror">
                    <div class="form-text">JPG, PNG or WebP, up to 2 MB.</div>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary flex-fill">{{ $editing ? 'Save changes' : 'Submit for review' }}</button>
                <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
