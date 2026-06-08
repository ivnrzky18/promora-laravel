<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterSellerRequest;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerAuthController extends Controller
{
    /**
     * Show the seller registration form.
     */
    public function showRegister(): View
    {
        return view('auth.seller.register');
    }

    /**
     * Handle seller registration.
     * Creates a new user with role=seller, creates SellerProfile, logs them in.
     */
    public function register(RegisterSellerRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'seller',
        ]);

        SellerProfile::create([
            'user_id'           => $user->id,
            'business_name'     => $request->business_name,
            'business_category' => $request->business_category,
            'address'           => $request->address,
            'description'       => $request->description,
        ]);

        Auth::login($user);

        return redirect()->route('seller.dashboard');
    }

    /**
     * Show the seller login form.
     */
    public function showLogin(): View
    {
        return view('auth.seller.login');
    }

    /**
     * Handle seller login attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('seller.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Log the seller out.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
