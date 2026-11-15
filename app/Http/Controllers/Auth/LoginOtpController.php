<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginOtpController extends Controller
{
    public function __construct(private readonly OtpService $otps) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('pending_login_id')) {
            return redirect()->route('login');
        }

        return view('auth.login-otp');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('pending_login_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->otps->verify($user->email, OtpService::LOGIN, $request->code)) {
            return back()->withErrors(['code' => 'That code is incorrect or has expired. Please check and try again.']);
        }

        $remember = $request->session()->pull('pending_login_remember', false);
        $request->session()->forget('pending_login_id');

        Auth::loginUsingId($userId, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /** Resend a fresh OTP to the pending user's email. */
    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('pending_login_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $otp = $this->otps->generate($user->email, OtpService::LOGIN);
        $this->otps->send($otp, $user);

        return back()->with('status', 'A new code has been sent to your email address.');
    }
}
