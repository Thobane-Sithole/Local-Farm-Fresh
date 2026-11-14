<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasUsername
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! auth()->user()->hasUsername()) {
            session()->put('url.intended', $request->url());
            return redirect()->route('username.edit')
                ->with('status', 'Please choose a community username before posting.');
        }

        return $next($request);
    }
}
