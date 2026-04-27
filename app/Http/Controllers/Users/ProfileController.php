<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->userProfile;
        return view('market.profile', compact('user', 'profile'));
    }

    public function settings()
    {
        $user = Auth::user();
        $profile = $user->userProfile;
        return view('market.settings', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->userProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'address']);

        if ($request->hasFile('profile_photo')) {
            if ($profile->profile_photo) {
                Storage::disk('public')->delete($profile->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['profile_photo'] = $path;
        }

        $profile->update($data);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        $profile = $user->userProfile;

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $profile->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address ?? $profile->address,
        ]);

        return response()->json(['success' => true, 'message' => 'Lokasi berhasil diperbarui!']);
    }
}
