<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationEmail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }
    
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        $remember = $request->has('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        if (!$user->is_active) {
            return redirect()->route('activation.notice', $user->email)
                ->with('error', 'Akun Anda belum aktif. Silakan cek email Anda.');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return match($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'sellers' => redirect()->route('sellers.dashboard'),
            'users'   => redirect()->route('market'),
            default   => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showUserRegister()
    {
        return view('auth.register-user');
    }

    public function showSellerRegister()
    {
        return view('auth.register-seller');
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $token = Str::random(64);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
            'is_active' => false,
            'activation_token' => $token
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $url = route('activate.account', ['token' => $token]);

        Mail::to($user->email)->send(new ActivationEmail(
            $request->name,
            $url
        ));

        return redirect()->route('activation.notice', ['email' => $user->email]);
    }

    public function registerSeller(Request $request)
    {
        $request->validate([
            'business_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable',
        ]);

        $token = Str::random(64);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'seller',
            'is_active' => false,
            'activation_token' => $token
        ]);

        SellerProfile::create([
            'user_id' => $user->id,
            'brand_name' => $request->business_name,
            'phone' => $request->phone,
        ]);

        $url = route('activate.account', ['token' => $token]);

        Mail::to($user->email)->send(new ActivationEmail(
            $request->name,
            $url
        ));

       return redirect()->route('activation.notice', ['email' => $user->email]);
    }

   public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Token tidak valid!');
        }

        $user->update([
            'is_active' => true,
            'activation_token' => null
        ]);

        return view('auth.activation-success');
    }

    public function resendActivation($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Email tidak ditemukan.');
        }

        if ($user->is_active) {
            return redirect()->route('login')->with('success', 'Akun sudah aktif, silakan login.');
        }

        $token = Str::random(64);

        $user->update([
            'activation_token' => $token
        ]);

        $url = route('activate.account', ['token' => $token]);

        Mail::to($user->email)->send(new ActivationEmail(
            $user->email,
            $url
        ));

        return back()->with('message', 'Email aktivasi berhasil dikirim ulang!');
    }
}
