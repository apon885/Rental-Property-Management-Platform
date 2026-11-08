@props(['property'])
<a href="{{ route('properties.show', $property) }}" class="pcard">
    <img class="pcard-img" src="{{ $property->image_url }}" alt="{{ $property->title }}" loading="lazy">
    <div class="pcard-body">
        <div class="pcard-rent">৳{{ number_format($property->rent) }} <small>/ month</small></div>
        <div class="pcard-title">{{ $property->title }}</div>
        <div class="pcard-loc"><i class="fa-solid fa-location-dot me-1"></i>{{ $property->area }}, {{ $property->city }}</div>
        <div class="pcard-meta">
            <span><i class="fa-solid fa-bed me-1"></i>{{ $property->bedrooms }}</span>
            <span><i class="fa-solid fa-bath me-1"></i>{{ $property->bathrooms }}</span>
            @if ($property->size_sqft)<span><i class="fa-solid fa-ruler-combined me-1"></i>{{ number_format($property->size_sqft) }} sqft</span>@endif
        </div>
    </div>
</a>
