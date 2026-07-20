<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware('role:admin') or ->middleware('role:admin,teacher')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You are not authorized to view this page.');
        }

        // If an admin disables this account while the teacher already has an
        // active session, kick them out immediately instead of letting the
        // session ride until it expires — same reason is shown on /login.
        if ($user->isDisabled()) {
            $reason = $user->disabled_reason
                ? "Your account has been disabled by the administrator. Reason: {$user->disabled_reason}"
                : 'Your account has been disabled by the administrator. Please contact your school admin.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $reason]);
        }

        return $next($request);
    }
}
