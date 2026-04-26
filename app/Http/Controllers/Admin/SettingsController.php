<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Billboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        $billboards = Billboard::orderBy('order')->get();
        return view('admin.settings', compact('settings', 'billboards'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function storeBillboard(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'gradient_from' => 'required|string',
            'gradient_to' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('billboards', 'public');
        }

        Billboard::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'gradient_from' => $request->gradient_from,
            'gradient_to' => $request->gradient_to,
            'image_path' => $imagePath,
            'order' => Billboard::max('order') + 1,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Billboard added successfully.');
    }

    public function updateBillboard(Request $request, Billboard $billboard)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'gradient_from' => 'required|string',
            'gradient_to' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'subtitle', 'gradient_from', 'gradient_to', 'is_active', 'order']);
        
        if ($request->hasFile('image')) {
            if ($billboard->image_path) {
                Storage::disk('public')->delete($billboard->image_path);
            }
            $data['image_path'] = $request->file('image')->store('billboards', 'public');
        }

        $billboard->update($data);

        return redirect()->back()->with('success', 'Billboard updated successfully.');
    }

    public function destroyBillboard(Billboard $billboard)
    {
        if ($billboard->image_path) {
            Storage::disk('public')->delete($billboard->image_path);
        }
        $billboard->delete();
        return redirect()->back()->with('success', 'Billboard deleted successfully.');
    }

    public function toggleBillboard(Billboard $billboard)
    {
        $billboard->update(['is_active' => !$billboard->is_active]);
        return redirect()->back()->with('success', 'Billboard status toggled.');
    }
}
