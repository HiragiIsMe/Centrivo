<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $locations = $query->latest()->get();

        return view('sellers-dashboard.locations', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'province'   => 'required',
            'city'       => 'required',
            'district'   => 'required',
            'address'    => 'required',
            'latitude'   => 'required',
            'longitude'  => 'required',
        ]);

        Location::create([
            'user_id'     => Auth::id(),
            'province'    => $request->province,
            'city'        => $request->city,
            'district'    => $request->district,
            'postal_code' => $request->postal_code,
            'address'     => $request->address,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return back()->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function update(Request $request, Location $location)
    {
        if ($location->user_id !== Auth::id()) {
            abort(403);
        }

        $location->update([
            'province'    => $request->province,
            'city'        => $request->city,
            'district'    => $request->district,
            'postal_code' => $request->postal_code,
            'address'     => $request->address,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return back()->with('success', 'Lokasi berhasil diupdate!');
    }

    public function destroy(Location $location)
    {
        if ($location->user_id !== Auth::id()) {
            abort(403);
        }

        $location->delete();

        return back()->with('success', 'Lokasi berhasil dihapus!');
    }
}