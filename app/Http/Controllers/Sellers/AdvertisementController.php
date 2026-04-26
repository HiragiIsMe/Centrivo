<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\AdPackage;
use App\Models\Advertisement;
use App\Models\AdvertisementTransaction;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class AdvertisementController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        $packages = AdPackage::where('is_active', true)->orderBy('duration_days')->get();

        $myServices = Service::where('seller_id', $seller->id)
            ->where('status', 'active')
            ->where('is_banned', false)
            ->with('activeAdvertisement')
            ->get();

        $myAdHistory = AdvertisementTransaction::with(['advertisement.service', 'adPackage'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->get();

        return view('sellers-dashboard.advertisements', compact('packages', 'myServices', 'myAdHistory'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'ad_package_id' => 'required|exists:ad_packages,id',
        ]);

        $seller = Auth::user();
        $service = Service::where('seller_id', $seller->id)->findOrFail($request->service_id);
        $package = AdPackage::where('is_active', true)->findOrFail($request->ad_package_id);

        // Check if service already has an active ad
        if ($service->activeAdvertisement) {
            return redirect()->back()->with('error', 'Layanan ini sudah memiliki iklan aktif hingga ' . $service->activeAdvertisement->end_date->format('d M Y, H:i'));
        }

        // Create or find the advertisement record for this service
        $advertisement = Advertisement::firstOrCreate(
            ['service_id' => $service->id],
            ['is_active' => false]
        );

        // Create the ad transaction
        $adTx = AdvertisementTransaction::create([
            'advertisement_id' => $advertisement->id,
            'seller_id' => $seller->id,
            'ad_package_id' => $package->id,
            'amount' => $package->price,
            'duration_days' => $package->duration_days,
            'payment_status' => 'pending',
            'payment_method' => 'midtrans',
        ]);

        // Midtrans Snap
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'ADV-' . $adTx->id . '-' . time(),
                'gross_amount' => (int) $package->price,
            ],
            'customer_details' => [
                'first_name' => $seller->sellerProfile->brand_name ?? $seller->email,
                'email' => $seller->email,
            ],
            'item_details' => [
                [
                    'id' => 'ADPKG-' . $package->id,
                    'price' => (int) $package->price,
                    'quantity' => 1,
                    'name' => 'Iklan: ' . substr($package->name, 0, 30) . ' - ' . substr($service->service_name, 0, 15),
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $adTx->update(['snap_token' => $snapToken]);

        return redirect()->route('seller.advertisements.pay', $adTx->id);
    }

    public function pay(AdvertisementTransaction $advertisementTransaction)
    {
        $seller = Auth::user();

        if ($advertisementTransaction->seller_id !== $seller->id) {
            abort(403);
        }

        $adTx = $advertisementTransaction;

        return view('sellers-dashboard.payment-ad', compact('adTx'));
    }
}
