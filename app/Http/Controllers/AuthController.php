<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $maxAttempts = (int) Setting::get('max_login_attempts', 5);
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Check if user is locked out due to excessive failed attempts
        if ($maxAttempts > 0 && RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Clear rate limiting on success
            if ($maxAttempts > 0) {
                RateLimiter::clear($throttleKey);
            }

            $request->session()->regenerate();

            if (Auth::user()->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your staff account has been deactivated. Contact the administrator.']);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        // Record failed login attempt (decay 60 seconds)
        if ($maxAttempts > 0) {
            RateLimiter::hit($throttleKey, 60);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our staff records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
