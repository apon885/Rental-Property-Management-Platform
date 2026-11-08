<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Booking extends Model
{
    protected $fillable = [
        'rental_request_id', 'property_id', 'tenant_id', 'landlord_id',
        'start_date', 'end_date', 'duration_months', 'monthly_rent', 'status',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'monthly_rent' => 'decimal:2',
    ];

    public function property(): BelongsTo      { return $this->belongsTo(Property::class); }
    public function tenant(): BelongsTo        { return $this->belongsTo(User::class, 'tenant_id'); }
    public function landlord(): BelongsTo      { return $this->belongsTo(User::class, 'landlord_id'); }
    public function rentalRequest(): BelongsTo { return $this->belongsTo(RentalRequest::class); }
    public function payments(): HasMany        { return $this->hasMany(Payment::class); }

    /** Billing months (Y-m) in this contract that have no payment yet. */
    public function unpaidMonths(): Collection
    {
        $paid   = $this->payments()->pluck('billing_month')->all();
        $months = collect();
        $first  = $this->start_date->copy()->startOfMonth();

        for ($i = 0; $i < $this->duration_months; $i++) {
            $key = $first->copy()->addMonthsNoOverflow($i)->format('Y-m');
            if (! in_array($key, $paid, true)) {
                $months->push($key);
            }
        }

        return $months;
    }

    /** Unpaid months that are due now (this month or earlier). */
    public function dueMonths(): Collection
    {
        $current = now()->format('Y-m');

        return $this->unpaidMonths()->filter(fn ($m) => $m <= $current)->values();
    }
}
