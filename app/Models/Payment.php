<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = [
        'bkash'  => 'bKash',
        'nagad'  => 'Nagad',
        'rocket' => 'Rocket',
        'card'   => 'Debit / credit card',
        'bank'   => 'Bank transfer',
    ];

    protected $fillable = [
        'booking_id', 'tenant_id', 'amount', 'billing_month', 'method',
        'transaction_id', 'status', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function tenant(): BelongsTo  { return $this->belongsTo(User::class, 'tenant_id'); }

    public function getMonthLabelAttribute(): string
    {
        return Carbon::parse($this->billing_month . '-01')->format('F Y');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? ucfirst($this->method);
    }
}
