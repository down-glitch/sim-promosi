<?php

namespace App\Http\Controllers;

use App\Models\AppUsers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function handleLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan username
        $user = AppUsers::where('USER_ID', $credentials['username'])->first();

        // Validasi username dan password
        if (!$user || $user->PASSWORD !== $credentials['password']) {
            return back()->withErrors([
                'login_error' => 'Username atau password salah!'
            ])->onlyInput('username');
        }

        // Set session
        $sessionData = [
            'user_id' => $user->USER_ID,
            'user_name' => $user->NAMA_LENGKAP,
            'user_email' => $user->EMAIL,
            'is_authenticated' => true,
            'login_time' => now()->toDateTimeString()
        ];
        
        session($sessionData);

        // Regenerate session ID untuk security
        session()->regenerate();

        // Return JSON untuk debugging
        return redirect()->route('dashboard')->with('success', 'Login berhasil!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Clear all session data
        session()->flush();
        
        // Invalidate the session
        session()->invalidate();
        
        // Regenerate CSRF token
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
