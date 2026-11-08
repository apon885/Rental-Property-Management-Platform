<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('type', 30);
            $table->string('address');
            $table->string('area', 80);
            $table->string('city', 80)->default('Dhaka');
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);
            $table->unsignedInteger('size_sqft')->nullable();
            $table->decimal('rent', 10, 2);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->string('amenities')->nullable();
            $table->string('image')->nullable();
            $table->string('status', 20)->default('pending');   // pending | approved | rejected
            $table->boolean('is_available')->default(true);
            $table->date('available_from')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_available']);
            $table->index('area');
        });

        Schema::create('rental_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->date('move_in_date');
            $table->unsignedTinyInteger('duration_months')->default(12);
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');   // pending | approved | rejected | cancelled
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('duration_months')->default(12);
            $table->decimal('monthly_rent', 10, 2);
            $table->string('status', 20)->default('active');    // active | completed | cancelled
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('billing_month', 7);                 // e.g. 2026-11
            $table->string('method', 20);                       // bkash | nagad | rocket | card | bank
            $table->string('transaction_id', 60)->unique();
            $table->string('status', 20)->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'billing_month']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('rental_requests');
        Schema::dropIfExists('properties');
    }
};
