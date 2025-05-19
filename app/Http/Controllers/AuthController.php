<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Tambahkan method yang hilang
    public function showLoginForm()
    {
        return view('login'); // Sesuaikan dengan nama view Anda
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $remember = $request->has('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => url('/dashboard')
                ]);
            }
            return redirect()->intended('dashboard');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'username' => ['Invalid credentials']
                ]
            ], 401);
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}