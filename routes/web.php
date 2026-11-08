<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Landlord\LandlordController;
use App\Http\Controllers\Landlord\PropertyController as LandlordPropertyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/* ---------- Public ---------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

/* ---------- Guests only: register + login (admin can NOT register) ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/* ---------- Signed-in users ---------- */
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {

    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->dashboardRoute()))->name('dashboard');
    Route::get('/home', fn () => redirect()->route('dashboard'));

    // Account management (all roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/verification', [ProfileController::class, 'requestVerification'])->name('profile.verify');

    /* ----- Tenant dashboard ----- */
    Route::prefix('tenant')->name('tenant.')->middleware(RoleMiddleware::class . ':tenant')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'dashboard'])->name('dashboard');
        Route::post('/properties/{property}/request', [TenantController::class, 'storeRequest'])->name('requests.store');
        Route::get('/requests', [TenantController::class, 'requests'])->name('requests.index');
        Route::delete('/requests/{rentalRequest}', [TenantController::class, 'cancelRequest'])->name('requests.cancel');
        Route::get('/bookings', [TenantController::class, 'bookings'])->name('bookings.index');
        Route::get('/payments', [TenantController::class, 'payments'])->name('payments.index');
        Route::get('/bookings/{booking}/pay', [TenantController::class, 'createPayment'])->name('payments.create');
        Route::post('/bookings/{booking}/pay', [TenantController::class, 'storePayment'])->name('payments.store');
    });

    /* ----- Landlord dashboard ----- */
    Route::prefix('landlord')->name('landlord.')->middleware(RoleMiddleware::class . ':landlord')->group(function () {
        Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard');

        Route::resource('properties', LandlordPropertyController::class)->except(['show']);
        Route::patch('/properties/{property}/availability', [LandlordPropertyController::class, 'toggleAvailability'])->name('properties.availability');

        Route::get('/requests', [LandlordController::class, 'requests'])->name('requests.index');
        Route::patch('/requests/{rentalRequest}/approve', [LandlordController::class, 'approve'])->name('requests.approve');
        Route::patch('/requests/{rentalRequest}/reject', [LandlordController::class, 'reject'])->name('requests.reject');

        Route::get('/tenants', [LandlordController::class, 'tenants'])->name('tenants.index');
        Route::patch('/bookings/{booking}/end', [LandlordController::class, 'endBooking'])->name('bookings.end');
        Route::get('/payments', [LandlordController::class, 'payments'])->name('payments.index');
    });

    /* ----- Admin dashboard (accounts are created by seeder / artisan only) ----- */
    Route::prefix('admin')->name('admin.')->middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}/status', [AdminController::class, 'toggleStatus'])->name('users.status');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/verifications', [AdminController::class, 'verifications'])->name('verifications.index');
        Route::patch('/users/{user}/verify', [AdminController::class, 'reviewUser'])->name('users.verify');

        Route::get('/properties', [AdminController::class, 'properties'])->name('properties.index');
        Route::patch('/properties/{property}/review', [AdminController::class, 'reviewProperty'])->name('properties.review');
        Route::delete('/properties/{property}', [AdminController::class, 'destroyProperty'])->name('properties.destroy');

        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
        Route::get('/payments/export', [AdminController::class, 'exportPayments'])->name('payments.export');

        Route::get('/security', [AdminController::class, 'security'])->name('security.index');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
    });
});
