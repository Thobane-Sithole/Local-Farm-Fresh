<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly OtpService $otps) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Validates credentials then redirects to the OTP screen for 2FA.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validates credentials and throws ValidationException on failure
        $request->authenticate();

        // Grab the now-authenticated user before we log them back out
        $user = Auth::user();

        // Log out temporarily — we complete the login after OTP verification
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        $request->session()->put('pending_login_id', $user->id);
        $request->session()->put('pending_login_remember', $request->boolean('remember'));

        $otp = $this->otps->generate($user->email, OtpService::LOGIN);
        $this->otps->send($otp, $user);

        return redirect()->route('login.otp');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
