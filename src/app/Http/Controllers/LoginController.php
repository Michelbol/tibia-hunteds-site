<?php

namespace App\Http\Controllers;

use App\User\Request\LoginRequestValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller {

    public function login(LoginRequestValidator $request): RedirectResponse {
        $login = $request->get('email');
        $password = $request->get('password');

        if (Auth::attempt(['email' => $login, 'password' => $password])) {
            return redirect()->route('home');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function index(): View {
        return view('login');
    }

    public function logout() {

    }
}
