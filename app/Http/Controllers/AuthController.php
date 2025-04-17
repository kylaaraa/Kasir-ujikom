<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input dari form login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Kredensial untuk login
        $credentials = [
            'email' => $request->email, 
            'password' => $request->password,
        ];

        // Mengecek kredensial untuk autentikasi
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');  
        }

        // Jika login gagal, kembali dengan pesan error
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]); 
    }

    // Logout pengguna
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
