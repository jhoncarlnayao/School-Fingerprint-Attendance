<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::user();

        // Only admins and teachers are allowed to use this login form.
        if (! in_array($user->role, ['admin', 'teacher'], true)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'This account is not authorized to sign in here.',
            ]);
        }

        // Disabled accounts (currently only teachers can be disabled) are
        // blocked from signing in — the admin-provided reason is surfaced
        // right here on the login form so the teacher knows why.
        if ($user->isDisabled()) {
            Auth::logout();

            $reason = $user->disabled_reason
                ? "Your account has been disabled by the administrator. Reason: {$user->disabled_reason}"
                : 'Your account has been disabled by the administrator. Please contact your school admin.';

            throw ValidationException::withMessages([
                'email' => $reason,
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($user->dashboardRoute());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
