<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterConsumerRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConsumerAuthController extends Controller
{
    /**
     * Show the consumer registration form.
     */
    public function showRegister(): View
    {
        return view('auth.consumer.register');
    }

    /**
     * Handle consumer registration.
     * Creates a new user with role=consumer, logs them in, and redirects to dashboard.
     */
    public function register(RegisterConsumerRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'consumer',
            'location' => $request->location,
        ]);

        Auth::login($user);

        return redirect()->route('consumer.dashboard');
    }

    /**
     * Show the consumer login form.
     */
    public function showLogin(): View
    {
        return view('auth.consumer.login');
    }

    /**
     * Handle consumer login attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('consumer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Log the consumer out.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
