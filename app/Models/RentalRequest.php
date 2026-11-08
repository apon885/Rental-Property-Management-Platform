<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRequest extends Model
{
    protected $fillable = ['property_id', 'tenant_id', 'move_in_date', 'duration_months', 'message', 'status'];

    protected $casts = ['move_in_date' => 'date'];

    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function tenant(): BelongsTo   { return $this->belongsTo(User::class, 'tenant_id'); }
}
