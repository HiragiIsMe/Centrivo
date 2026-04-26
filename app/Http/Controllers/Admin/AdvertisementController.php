<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPackage;
use App\Models\Advertisement;
use App\Models\AdvertisementTransaction;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $packages = AdPackage::orderBy('duration_days', 'asc')->get();

        $searchQuery = $request->get('search', '');

        $subscribersQuery = AdvertisementTransaction::with(['advertisement.service', 'seller.sellerProfile', 'adPackage'])
            ->where('payment_status', 'paid')
            ->latest();

        if (!empty($searchQuery)) {
            $subscribersQuery->whereHas('seller.sellerProfile', function ($q) use ($searchQuery) {
                $q->where('brand_name', 'like', "%{$searchQuery}%");
            })->orWhereHas('seller', function ($q) use ($searchQuery) {
                $q->where('email', 'like', "%{$searchQuery}%");
            })->orWhereHas('advertisement.service', function ($q) use ($searchQuery) {
                $q->where('service_name', 'like', "%{$searchQuery}%");
            });
        }

        $subscribers = $subscribersQuery->paginate(15);

        // Stats
        $totalActiveAds = Advertisement::where('is_active', true)->where('end_date', '>', now())->count();
        $totalRevenue = AdvertisementTransaction::where('payment_status', 'paid')->sum('amount');

        return view('admin.advertisements', compact('packages', 'subscribers', 'searchQuery', 'totalActiveAds', 'totalRevenue'));
    }

    public function storePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1|max:365',
            'price' => 'required|numeric|min:1000',
        ]);

        AdPackage::create([
            'name' => $request->name,
            'duration_days' => $request->duration_days,
            'price' => $request->price,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Paket iklan berhasil dibuat.');
    }

    public function togglePackage(AdPackage $adPackage)
    {
        $adPackage->update(['is_active' => !$adPackage->is_active]);

        return redirect()->back()->with('success', 'Status paket berhasil diperbarui.');
    }

    public function destroyPackage(AdPackage $adPackage)
    {
        $adPackage->delete();

        return redirect()->back()->with('success', 'Paket iklan berhasil dihapus.');
    }
}
