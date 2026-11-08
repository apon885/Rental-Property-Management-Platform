<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    public const TYPES = [
        'apartment'  => 'Apartment',
        'house'      => 'House',
        'room'       => 'Single room',
        'commercial' => 'Commercial space',
    ];

    protected $fillable = [
        'landlord_id', 'title', 'description', 'type', 'address', 'area', 'city',
        'bedrooms', 'bathrooms', 'size_sqft', 'rent', 'deposit', 'amenities',
        'image', 'status', 'is_available', 'available_from',
    ];

    protected $casts = [
        'is_available'   => 'boolean',
        'available_from' => 'date',
        'rent'           => 'decimal:2',
        'deposit'        => 'decimal:2',
    ];

    public function landlord(): BelongsTo      { return $this->belongsTo(User::class, 'landlord_id'); }
    public function rentalRequests(): HasMany  { return $this->hasMany(RentalRequest::class); }
    public function bookings(): HasMany        { return $this->hasMany(Booking::class); }

    /** Approved by admin and currently free to rent. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->where('status', 'approved')->where('is_available', true);
    }

    public function scopeSearch(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('title', 'like', "%{$term}%")
                      ->orWhere('area', 'like', "%{$term}%")
                      ->orWhere('address', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($f['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($f['area'] ?? null, fn ($q, $v) => $q->where('area', 'like', "%{$v}%"))
            ->when($f['min_rent'] ?? null, fn ($q, $v) => $q->where('rent', '>=', $v))
            ->when($f['max_rent'] ?? null, fn ($q, $v) => $q->where('rent', '<=', $v))
            ->when($f['bedrooms'] ?? null, fn ($q, $v) => $q->where('bedrooms', '>=', $v))
            ->when($f['sort'] ?? 'latest', function ($q, $sort) {
                return match ($sort) {
                    'rent_asc'  => $q->orderBy('rent'),
                    'rent_desc' => $q->orderByDesc('rent'),
                    default     => $q->latest(),
                };
            });
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.svg');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getAmenityListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->amenities))));
    }
}
