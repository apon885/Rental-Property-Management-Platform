<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RentalRequest;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /** Property discovery: search + filter + sort. */
    public function index(Request $request)
    {
        $request->validate([
            'min_rent' => ['nullable', 'numeric', 'min:0'],
            'max_rent' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:10'],
            'type'     => ['nullable', 'in:' . implode(',', array_keys(Property::TYPES))],
            'sort'     => ['nullable', 'in:latest,rent_asc,rent_desc'],
        ]);

        $filters    = $request->only(['q', 'type', 'area', 'min_rent', 'max_rent', 'bedrooms', 'sort']);
        $properties = Property::listed()->with('landlord')->search($filters)->paginate(9)->withQueryString();
        $areas      = Property::listed()->select('area')->distinct()->orderBy('area')->pluck('area');

        return view('properties.index', compact('properties', 'filters', 'areas'));
    }

    public function show(Property $property)
    {
        $user       = auth()->user();
        $canPreview = $user && ($user->isAdmin() || $user->id === $property->landlord_id);

        abort_unless($property->status === 'approved' || $canPreview, 404);

        $property->load('landlord');

        $pendingRequest = ($user && $user->isTenant())
            ? RentalRequest::where('property_id', $property->id)->where('tenant_id', $user->id)->where('status', 'pending')->first()
            : null;

        $similar = Property::listed()
            ->where('id', '!=', $property->id)
            ->where(fn ($q) => $q->where('area', $property->area)->orWhere('type', $property->type))
            ->latest()->take(3)->get();

        return view('properties.show', compact('property', 'pendingRequest', 'similar'));
    }
}
