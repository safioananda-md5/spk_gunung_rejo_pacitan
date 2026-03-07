<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            $name = Auth::user()->role . '.dashboard';
            return redirect(route($name));
        } else {
            return view('auth.login');
        }
    }

    public function post(Request $request)
    {
        // dd($request->all());
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            flash()->success('Login berhasil, Selamat datang!');

            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            flash()->error('Login gagal, Kredensial akun tidak valid!');
            return back()->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        flash()->success('Logout berhasil, Sampai jumpa kembali!');

        return redirect(route('login'));
    }
}
