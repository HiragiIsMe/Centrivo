<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->role == 'user') {
            if ($user->is_banned) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $code = $user->ban_report_code ?? '';
                return redirect()->route('banned.notice', $code)
                    ->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh administrator.']);
            }
            return $next($request);
        }

        return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
