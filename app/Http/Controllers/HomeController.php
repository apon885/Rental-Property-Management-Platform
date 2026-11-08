<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Property::listed()->latest()->take(6)->get();
        $areas    = Property::listed()->select('area')->distinct()->orderBy('area')->pluck('area');
        $counts   = [
            'listings'  => Property::listed()->count(),
            'landlords' => User::where('role', 'landlord')->count(),
            'tenants'   => User::where('role', 'tenant')->count(),
        ];

        return view('home', compact('featured', 'areas', 'counts'));
    }
}
