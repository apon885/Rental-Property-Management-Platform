<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            ['label' => 'Users', 'value' => User::where('role', '!=', 'admin')->count(), 'hint' => User::where('role', 'landlord')->count() . ' landlords, ' . User::where('role', 'tenant')->count() . ' tenants'],
            ['label' => 'Properties', 'value' => Property::count(), 'hint' => Property::where('status', 'pending')->count() . ' awaiting review'],
            ['label' => 'Active bookings', 'value' => Booking::where('status', 'active')->count()],
            ['label' => 'Revenue processed', 'value' => '৳' . number_format(Payment::sum('amount'))],
            ['label' => 'Pending checks', 'value' => User::where('verification_status', 'pending')->count() + Property::where('status', 'pending')->count()],
        ];

        $pendingUsers      = User::where('verification_status', 'pending')->latest()->take(5)->get();
        $pendingProperties = Property::where('status', 'pending')->with('landlord')->latest()->take(5)->get();
        $logs              = ActivityLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'pendingProperties', 'logs'));
    }

    /* ---------- Users ---------- */
    public function users(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $t) => $q->where(fn ($w) => $w->where('name', 'like', "%{$t}%")->orWhere('email', 'like', "%{$t}%")->orWhere('phone', 'like', "%{$t}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(12)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Administrator accounts cannot be suspended here.');
        }

        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        ActivityLog::record('user_' . $user->status, ucfirst($user->status) . " account: {$user->email}");

        return back()->with('success', $user->status === 'active' ? 'Account reactivated.' : 'Account suspended.');
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Administrator accounts cannot be deleted here.');
        }

        ActivityLog::record('user_deleted', "Deleted account: {$user->email}");
        $user->delete();

        return back()->with('success', 'Account deleted.');
    }

    /* ---------- Verification ---------- */
    public function verifications()
    {
        $users      = User::where('verification_status', 'pending')->latest()->get();
        $properties = Property::where('status', 'pending')->with('landlord')->latest()->get();

        return view('admin.verifications', compact('users', 'properties'));
    }

    public function reviewUser(Request $request, User $user)
    {
        $decision = $request->validate(['decision' => ['required', 'in:approve,reject']])['decision'];

        $user->update(['verification_status' => $decision === 'approve' ? 'verified' : 'rejected']);
        ActivityLog::record('user_verification', ucfirst($decision) . "d identity of {$user->email}");

        return back()->with('success', 'Identity ' . ($decision === 'approve' ? 'verified' : 'rejected') . '.');
    }

    /* ---------- Properties ---------- */
    public function properties(Request $request)
    {
        $properties = Property::with('landlord')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $t) => $q->where('title', 'like', "%{$t}%"))
            ->latest()->paginate(12)->withQueryString();

        return view('admin.properties', compact('properties'));
    }

    public function reviewProperty(Request $request, Property $property)
    {
        $decision = $request->validate(['decision' => ['required', 'in:approve,reject']])['decision'];

        $property->update(['status' => $decision === 'approve' ? 'approved' : 'rejected']);
        ActivityLog::record('property_review', ucfirst($decision) . "d listing \"{$property->title}\"");

        return back()->with('success', 'Listing ' . ($decision === 'approve' ? 'approved' : 'rejected') . '.');
    }

    public function destroyProperty(Property $property)
    {
        if ($property->bookings()->where('status', 'active')->exists()) {
            return back()->with('error', 'This property has an active booking and cannot be deleted.');
        }

        ActivityLog::record('property_deleted', "Admin deleted listing \"{$property->title}\"");
        $property->delete();

        return back()->with('success', 'Listing deleted.');
    }

    /* ---------- Bookings & payments ---------- */
    public function bookings(Request $request)
    {
        $bookings = Booking::with(['property', 'tenant', 'landlord'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(12)->withQueryString();

        return view('admin.bookings', compact('bookings'));
    }

    public function payments()
    {
        $payments = Payment::with(['tenant', 'booking.property', 'booking.landlord'])->latest('paid_at')->paginate(15);
        $total    = Payment::sum('amount');

        return view('admin.payments', compact('payments', 'total'));
    }

    public function exportPayments()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Paid at', 'Tenant', 'Property', 'Month', 'Method', 'Transaction', 'Amount (BDT)', 'Status'], ',', '"', '\\');

            Payment::with(['tenant', 'booking.property'])->orderBy('id')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->id, optional($p->paid_at)->format('Y-m-d H:i'), $p->tenant->name ?? '-',
                        $p->booking->property->title ?? '-', $p->billing_month, $p->method,
                        $p->transaction_id, $p->amount, $p->status,
                    ], ',', '"', '\\');
                }
            });

            fclose($out);
        }, 'payments-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /* ---------- Security ---------- */
    public function security(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->latest()->paginate(15)->withQueryString();

        $since = now()->subDay();

        $summary = [
            'failed_24h'    => ActivityLog::where('action', 'login_failed')->where('created_at', '>=', $since)->count(),
            'blocked_24h'   => ActivityLog::where('action', 'login_blocked')->where('created_at', '>=', $since)->count(),
            'logins_24h'    => ActivityLog::where('action', 'login')->where('created_at', '>=', $since)->count(),
            'suspended'     => User::where('status', 'suspended')->count(),
        ];

        $suspiciousIps = ActivityLog::where('action', 'login_failed')->where('created_at', '>=', $since)
            ->selectRaw('ip_address, count(*) as attempts')->groupBy('ip_address')
            ->orderByDesc('attempts')->take(5)->get();

        $suspended = User::where('status', 'suspended')->latest()->take(8)->get();
        $actions   = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.security', compact('logs', 'summary', 'suspiciousIps', 'suspended', 'actions'));
    }

    /* ---------- Reports ---------- */
    public function reports()
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));

        $paid = Payment::where('paid_at', '>=', now()->subMonths(5)->startOfMonth())->get()
            ->groupBy(fn ($p) => $p->paid_at->format('Y-m'))
            ->map(fn ($g) => $g->sum('amount'));

        $revenue = [
            'labels' => $months->map(fn ($m) => \Carbon\Carbon::parse($m . '-01')->format('M Y'))->all(),
            'values' => $months->map(fn ($m) => (float) ($paid[$m] ?? 0))->all(),
        ];

        $byType = Property::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');

        $summary = [
            'users'           => User::where('role', '!=', 'admin')->count(),
            'properties'      => Property::count(),
            'listed'          => Property::listed()->count(),
            'requests'        => RentalRequest::count(),
            'approved'        => RentalRequest::where('status', 'approved')->count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'revenue'         => Payment::sum('amount'),
            'occupancy'       => Property::where('status', 'approved')->count() > 0
                ? round(Booking::where('status', 'active')->count() / Property::where('status', 'approved')->count() * 100)
                : 0,
        ];

        $topAreas = Property::where('status', 'approved')->selectRaw('area, count(*) as total, avg(rent) as avg_rent')
            ->groupBy('area')->orderByDesc('total')->take(5)->get();

        return view('admin.reports', compact('revenue', 'byType', 'summary', 'topAreas'));
    }
}
