<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $identifier = trim($credentials['email']);
        $loginField = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';
        $authCredentials = [
            $loginField => $identifier,
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (! Auth::attempt($authCredentials)) {
            return back()
                ->withErrors(['email' => 'Email/NIM atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (in_array($request->user()->role, ['mahasiswa', 'dosen'], true)) {
            return redirect()->route('user.dashboard');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->withErrors([
            'email' => 'Akun ini tidak memiliki akses ke aplikasi web.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
