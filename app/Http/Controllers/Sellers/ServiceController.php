<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\ServiceImage;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('images')->where('seller_id', Auth::id());

        if ($request->filled('search')) {
            $query->where('service_name', 'like', '%' . $request->search . '%');
        }

        $services = $query->latest()->get();
        $categories = Category::all();
        $locations = Location::where('user_id', Auth::id())->get();

        return view('sellers-dashboard.services', compact('services', 'categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
            'whatsapp' => 'required',
            'start_price' => 'required|numeric',
            'description' => 'required',
            'images'       => 'required|array|max:5',
            'images.*'     => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $service = Service::create([
            'seller_id'   => Auth::id(),
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'service_name'=> $request->service_name,
            'description' => $request->description,
            'start_price' => $request->start_price,
            'whatsapp'    => $request->whatsapp,
            'status'      => 'active'
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = time().'_'.uniqid().'.'.$img->getClientOriginalExtension();
                $path = $img->storeAs('services', $filename, 'public');
                ServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path
                ]);
            }
        }

        return back()->with('success', 'Service berhasil dibuat!');
    }

    public function update(Request $request, Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'service_name' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
            'whatsapp' => 'required',
            'start_price' => 'required|numeric',
            'description' => 'required',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $service->update([
            'service_name'=> $request->service_name,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'description' => $request->description,
            'start_price' => $request->start_price,
            'whatsapp'    => $request->whatsapp,
        ]);

        if ($request->hasFile('images')) {
            $currentImageCount = $service->images()->count();
            $newImageCount = count($request->file('images'));
            
            if (($currentImageCount + $newImageCount) > 5) {
                return back()->withErrors(['images' => 'Total gambar maksimal adalah 5. Hapus beberapa gambar terlebih dahulu.']);
            }

            foreach ($request->file('images') as $img) {
                $filename = time().'_'.uniqid().'.'.$img->getClientOriginalExtension();
                $path = $img->storeAs('services', $filename, 'public');
                ServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path
                ]);
            }
        }

        return back()->with('success', 'Service berhasil diupdate!');
    }

    public function destroy(Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        foreach ($service->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $service->delete();

        return back()->with('success', 'Service berhasil dihapus!');
    }

    public function destroyImage(ServiceImage $image)
    {
        if ($image->service->seller_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function toggleStatus(Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        $service->update([
            'status' => $service->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'status' => $service->status
        ]);
    }
}
