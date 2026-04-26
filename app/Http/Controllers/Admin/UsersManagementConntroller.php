<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersManagementConntroller extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'seller');
        $search = $request->get('search');

        $query = User::with(['userProfile', 'sellerProfile'])->withCount('reportsReceived');

        if ($tab === 'seller') {
            $query->where('role', 'seller');
        } else {
            $query->where('role', 'user');
        }

        if ($search) {
            $query->where(function($q) use ($search, $tab) {
                $q->where('email', 'like', "%{$search}%");
                if ($tab === 'seller') {
                    $q->orWhereHas('sellerProfile', function($sq) use ($search) {
                        $sq->where('brand_name', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhereHas('userProfile', function($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $users = $query->paginate(10)->appends(['tab' => $tab, 'search' => $search]);

        return view('dashboard.users-management', compact('users', 'tab', 'search'));
    }

    public function reports(User $user)
    {
        $reports = \App\Models\Report::with(['reporter.userProfile', 'reportedService'])
                         ->where('reported_user_id', $user->id)
                         ->latest()
                         ->get();
                         
        return response()->json([
            'user' => $user->load('userProfile', 'sellerProfile'),
            'reports' => $reports
        ]);
    }

    public function ban(User $user)
    {
        $user->update([
            'is_banned' => true,
            'banned_at' => now()
        ]);
        return back()->with('success', 'User has been banned successfully.');
    }

    public function unban(User $user)
    {
        $user->update([
            'is_banned' => false,
            'banned_at' => null
        ]);
        return back()->with('success', 'User has been unbanned successfully.');
    }
}
