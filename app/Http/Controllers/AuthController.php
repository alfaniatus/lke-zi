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
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'manager') {
            return redirect()->route('manager-area.dashboard'); 
        }

        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Role tidak dikenali.']);
    }

    return back()->withErrors(['email' => 'Login gagal. Email atau password salah.']);
}


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
