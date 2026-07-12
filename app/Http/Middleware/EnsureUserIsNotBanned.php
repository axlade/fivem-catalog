<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    /**
     * Catch users who were banned while they still had an active session —
     * the login form only blocks new sign-ins, this catches existing ones.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            $reason = Auth::user()->ban_reason;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => $reason
                    ? "Your account has been suspended: {$reason}"
                    : 'Your account has been suspended.',
            ]);
        }

        return $next($request);
    }
}
