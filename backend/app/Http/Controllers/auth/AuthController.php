<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        // dummy login (sementara)
        $email = $request->email;

        if ($email === 'admin@gmail.com') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/user/dashboard');
        }
    }

    public function logout()
    {
        return redirect('/login');
    }
}