<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutMinutes = (int) config('session.idle_timeout', config('session.lifetime', 30));
            $lastActivity   = $request->session()->get('_lff_last_activity');

            if ($lastActivity && (time() - $lastActivity) > ($timeoutMinutes * 60)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired.'], 401);
                }

                return redirect()->route('login')
                    ->with('status', 'Your session expired due to inactivity. Please log in again.');
            }

            $request->session()->put('_lff_last_activity', time());
        }

        return $next($request);
    }
}
