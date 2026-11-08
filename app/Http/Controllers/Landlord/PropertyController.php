<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    private function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['required', 'string', 'max:3000'],
            'type'           => ['required', 'in:' . implode(',', array_keys(Property::TYPES))],
            'address'        => ['required', 'string', 'max:255'],
            'area'           => ['required', 'string', 'max:80'],
            'city'           => ['required', 'string', 'max:80'],
            'bedrooms'       => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms'      => ['required', 'integer', 'min:0', 'max:20'],
            'size_sqft'      => ['nullable', 'integer', 'min:50', 'max:100000'],
            'rent'           => ['required', 'numeric', 'min:500', 'max:10000000'],
            'deposit'        => ['nullable', 'numeric', 'min:0', 'max:50000000'],
            'amenities'      => ['nullable', 'string', 'max:255'],
            'available_from' => ['nullable', 'date'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function own(Property $property): Property
    {
        abort_unless($property->landlord_id === auth()->id(), 403);

        return $property;
    }

    public function index()
    {
        $properties = Property::where('landlord_id', auth()->id())
            ->withCount(['rentalRequests as pending_requests_count' => fn ($q) => $q->where('status', 'pending')])
            ->latest()->paginate(10);

        return view('landlord.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('landlord.properties.form', ['property' => new Property(['city' => 'Dhaka', 'bedrooms' => 1, 'bathrooms' => 1])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['deposit'] = $data['deposit'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        $property = Property::create($data + [
            'landlord_id'  => auth()->id(),
            'status'       => 'pending',
            'is_available' => true,
        ]);

        ActivityLog::record('property_created', 'Listed "' . $property->title . '" (waiting for admin approval).');

        return redirect()->route('landlord.properties.index')
            ->with('success', 'Listing saved. It goes live once an admin approves it.');
    }

    public function edit(Property $property)
    {
        return view('landlord.properties.form', ['property' => $this->own($property)]);
    }

    public function update(Request $request, Property $property)
    {
        $this->own($property);
        $data = $request->validate($this->rules());
        $data['deposit'] = $data['deposit'] ?? 0;

        if ($request->hasFile('image')) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        // A rejected listing that gets edited goes back to review.
        if ($property->status === 'rejected') {
            $data['status'] = 'pending';
        }

        $property->update($data);
        ActivityLog::record('property_updated', 'Updated "' . $property->title . '".');

        return redirect()->route('landlord.properties.index')->with('success', 'Listing updated.');
    }

    public function destroy(Property $property)
    {
        $this->own($property);

        if ($property->bookings()->where('status', 'active')->exists()) {
            return back()->with('error', 'This property has an active tenant. End the tenancy first.');
        }

        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        ActivityLog::record('property_deleted', 'Deleted "' . $property->title . '".');
        $property->delete();

        return redirect()->route('landlord.properties.index')->with('success', 'Listing deleted.');
    }

    /** Availability toggle (listing manager). */
    public function toggleAvailability(Property $property)
    {
        $this->own($property);

        if (! $property->is_available && $property->bookings()->where('status', 'active')->exists()) {
            return back()->with('error', 'This property has an active tenant. End the tenancy first.');
        }

        $property->update(['is_available' => ! $property->is_available]);

        return back()->with('success', $property->is_available ? 'Marked as available.' : 'Marked as not available.');
    }
}
