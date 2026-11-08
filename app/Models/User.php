<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status',
        'verification_status', 'nid_number', 'address',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isLandlord(): bool { return $this->role === 'landlord'; }
    public function isTenant(): bool   { return $this->role === 'tenant'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function isVerified(): bool { return $this->verification_status === 'verified'; }

    public function dashboardRoute(): string
    {
        return $this->role . '.dashboard';
    }

    public function properties(): HasMany      { return $this->hasMany(Property::class, 'landlord_id'); }
    public function rentalRequests(): HasMany  { return $this->hasMany(RentalRequest::class, 'tenant_id'); }
    public function bookings(): HasMany        { return $this->hasMany(Booking::class, 'tenant_id'); }
    public function payments(): HasMany        { return $this->hasMany(Payment::class, 'tenant_id'); }
    public function activityLogs(): HasMany    { return $this->hasMany(ActivityLog::class); }
}
