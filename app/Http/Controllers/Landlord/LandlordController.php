<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandlordController extends Controller
{
    public function dashboard()
    {
        $id = auth()->id();

        $pendingRequests = RentalRequest::where('status', 'pending')
            ->whereHas('property', fn ($q) => $q->where('landlord_id', $id));

        $earnings = Payment::whereHas('booking', fn ($q) => $q->where('landlord_id', $id))->sum('amount');

        $stats = [
            ['label' => 'Listings', 'value' => Property::where('landlord_id', $id)->count(), 'hint' => Property::where('landlord_id', $id)->where('status', 'pending')->count() . ' awaiting approval'],
            ['label' => 'Available now', 'value' => Property::listed()->where('landlord_id', $id)->count()],
            ['label' => 'Pending requests', 'value' => (clone $pendingRequests)->count()],
            ['label' => 'Active tenants', 'value' => Booking::where('landlord_id', $id)->where('status', 'active')->count()],
            ['label' => 'Rent received', 'value' => '৳' . number_format($earnings)],
        ];

        $requests = (clone $pendingRequests)->with(['tenant', 'property'])->latest()->take(5)->get();

        $payments = Payment::whereHas('booking', fn ($q) => $q->where('landlord_id', $id))
            ->with(['tenant', 'booking.property'])->latest('paid_at')->take(5)->get();

        return view('landlord.dashboard', compact('stats', 'requests', 'payments'));
    }

    public function requests(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = RentalRequest::whereHas('property', fn ($q) => $q->where('landlord_id', auth()->id()))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->with(['tenant', 'property'])->latest()->paginate(10)->withQueryString();

        return view('landlord.requests', compact('requests', 'status'));
    }

    public function approve(RentalRequest $rentalRequest)
    {
        $rentalRequest->load('property');
        abort_unless($rentalRequest->property->landlord_id === auth()->id(), 403);

        if ($rentalRequest->status !== 'pending') {
            return back()->with('error', 'This request was already handled.');
        }

        $property = $rentalRequest->property;

        if ($property->status !== 'approved' || ! $property->is_available) {
            return back()->with('error', 'Mark the property as available (and approved) before accepting a tenant.');
        }

        DB::transaction(function () use ($rentalRequest, $property) {
            $rentalRequest->update(['status' => 'approved']);

            Booking::create([
                'rental_request_id' => $rentalRequest->id,
                'property_id'       => $property->id,
                'tenant_id'         => $rentalRequest->tenant_id,
                'landlord_id'       => $property->landlord_id,
                'start_date'        => $rentalRequest->move_in_date,
                'end_date'          => $rentalRequest->move_in_date->copy()->addMonthsNoOverflow($rentalRequest->duration_months)->subDay(),
                'duration_months'   => $rentalRequest->duration_months,
                'monthly_rent'      => $property->rent,
                'status'            => 'active',
            ]);

            $property->update(['is_available' => false]);

            // Other tenants waiting for the same property are declined automatically.
            RentalRequest::where('property_id', $property->id)->where('status', 'pending')->update(['status' => 'rejected']);
        });

        ActivityLog::record('request_approved', 'Approved a rental request for "' . $property->title . '".');

        return redirect()->route('landlord.tenants.index')->with('success', 'Request approved. A booking was created.');
    }

    public function reject(RentalRequest $rentalRequest)
    {
        $rentalRequest->load('property');
        abort_unless($rentalRequest->property->landlord_id === auth()->id(), 403);

        if ($rentalRequest->status === 'pending') {
            $rentalRequest->update(['status' => 'rejected']);
            ActivityLog::record('request_rejected', 'Rejected a rental request for "' . $rentalRequest->property->title . '".');
        }

        return back()->with('success', 'Request rejected.');
    }

    public function tenants()
    {
        $bookings = Booking::where('landlord_id', auth()->id())
            ->with(['tenant', 'property'])->withCount('payments')->latest()->paginate(10);

        return view('landlord.tenants', compact('bookings'));
    }

    public function endBooking(Booking $booking)
    {
        abort_unless($booking->landlord_id === auth()->id(), 403);

        if ($booking->status === 'active') {
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'completed']);
                $booking->property->update(['is_available' => true, 'available_from' => now()]);
            });
            ActivityLog::record('booking_ended', 'Ended a tenancy for "' . $booking->property->title . '".');
        }

        return back()->with('success', 'Tenancy ended. The property is available again.');
    }

    public function payments()
    {
        $base = Payment::whereHas('booking', fn ($q) => $q->where('landlord_id', auth()->id()));

        $total    = (clone $base)->sum('amount');
        $payments = $base->with(['tenant', 'booking.property'])->latest('paid_at')->paginate(12);

        return view('landlord.payments', compact('payments', 'total'));
    }
}
