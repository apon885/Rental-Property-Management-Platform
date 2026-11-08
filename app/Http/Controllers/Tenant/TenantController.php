<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $activeBookings = $user->bookings()->where('status', 'active')->with('property')->get();
        $dueBookings    = $activeBookings->filter(fn ($b) => $b->dueMonths()->isNotEmpty());

        $stats = [
            ['label' => 'Rental requests', 'value' => $user->rentalRequests()->count(), 'hint' => $user->rentalRequests()->where('status', 'pending')->count() . ' waiting for reply'],
            ['label' => 'Active bookings', 'value' => $activeBookings->count()],
            ['label' => 'Rent due', 'value' => $dueBookings->sum(fn ($b) => $b->dueMonths()->count()) . ' month(s)'],
            ['label' => 'Total paid', 'value' => '৳' . number_format($user->payments()->sum('amount'))],
        ];

        [$recommended, $reason] = $this->recommendations($user);

        $recentRequests = $user->rentalRequests()->with('property')->latest()->take(5)->get();

        return view('tenant.dashboard', compact('stats', 'recommended', 'reason', 'recentRequests', 'dueBookings'));
    }

    /** Simple recommendation engine: same areas / types the tenant has asked about before. */
    private function recommendations($user): array
    {
        $requested = $user->rentalRequests()->with('property')->get()->pluck('property')->filter();
        $areas     = $requested->pluck('area')->unique()->values();
        $types     = $requested->pluck('type')->unique()->values();

        $items  = collect();
        $reason = 'Newest approved listings';

        if ($requested->isNotEmpty()) {
            $items = Property::listed()
                ->whereNotIn('id', $requested->pluck('id'))
                ->where(function ($q) use ($areas, $types) {
                    $q->whereIn('area', $areas)->orWhereIn('type', $types);
                })
                ->latest()->take(6)->get();
            $reason = 'Picked from the areas and property types you asked about';
        }

        if ($items->isEmpty()) {
            $items  = Property::listed()->latest()->take(6)->get();
            $reason = 'Newest approved listings';
        }

        return [$items, $reason];
    }

    public function storeRequest(Request $request, Property $property)
    {
        if ($property->status !== 'approved' || ! $property->is_available) {
            return back()->with('error', 'This property is no longer available.');
        }

        $data = $request->validate([
            'move_in_date'    => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'message'         => ['nullable', 'string', 'max:500'],
        ]);

        $exists = RentalRequest::where('property_id', $property->id)
            ->where('tenant_id', auth()->id())->where('status', 'pending')->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending request for this property.');
        }

        RentalRequest::create($data + ['property_id' => $property->id, 'tenant_id' => auth()->id(), 'status' => 'pending']);
        ActivityLog::record('rental_request', 'Sent a rental request for "' . $property->title . '".');

        return redirect()->route('tenant.requests.index')->with('success', 'Request sent. The landlord will reply from their dashboard.');
    }

    public function requests()
    {
        $requests = auth()->user()->rentalRequests()->with('property.landlord')->latest()->paginate(10);

        return view('tenant.requests', compact('requests'));
    }

    public function cancelRequest(RentalRequest $rentalRequest)
    {
        abort_unless($rentalRequest->tenant_id === auth()->id(), 403);

        if ($rentalRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $rentalRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Request cancelled.');
    }

    public function bookings()
    {
        $bookings = auth()->user()->bookings()->with(['property', 'landlord'])->latest()->paginate(10);

        return view('tenant.bookings', compact('bookings'));
    }

    public function payments()
    {
        $user     = auth()->user();
        $payments = $user->payments()->with('booking.property')->latest('paid_at')->paginate(12);
        $total    = $user->payments()->sum('amount');

        return view('tenant.payments', compact('payments', 'total'));
    }

    public function createPayment(Booking $booking)
    {
        abort_unless($booking->tenant_id === auth()->id(), 403);

        if ($booking->status !== 'active') {
            return redirect()->route('tenant.bookings.index')->with('error', 'This booking is not active.');
        }

        $booking->load('property');
        $months = $booking->unpaidMonths();

        if ($months->isEmpty()) {
            return redirect()->route('tenant.bookings.index')->with('success', 'All months are paid. Nothing is due.');
        }

        return view('tenant.pay', compact('booking', 'months'));
    }

    public function storePayment(Request $request, Booking $booking)
    {
        abort_unless($booking->tenant_id === auth()->id() && $booking->status === 'active', 403);

        $months = $booking->unpaidMonths();

        $data = $request->validate([
            'billing_month'  => ['required', Rule::in($months->all())],
            'method'         => ['required', Rule::in(array_keys(Payment::METHODS))],
            'transaction_id' => ['required', 'regex:/^[A-Za-z0-9\-]{6,40}$/', 'unique:payments,transaction_id'],
        ], [
            'transaction_id.regex' => 'Use 6 to 40 letters, numbers or dashes.',
        ]);

        // Demo payment gateway: the payment is recorded as paid straight away.
        Payment::create([
            'booking_id'     => $booking->id,
            'tenant_id'      => auth()->id(),
            'amount'         => $booking->monthly_rent,
            'billing_month'  => $data['billing_month'],
            'method'         => $data['method'],
            'transaction_id' => $data['transaction_id'],
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        ActivityLog::record('payment', 'Paid rent for ' . $data['billing_month'] . ' (' . $booking->property->title . ').');

        return redirect()->route('tenant.payments.index')->with('success', 'Payment received. Thank you!');
    }
}
