<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OtpPasswordController extends Controller
{
    public function __construct(private readonly OtpService $otps) {}

    /** Step 1 — show the email entry form. */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Step 1 POST — generate and send OTP, then redirect to verify screen. */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        // Always appear to succeed to avoid email enumeration
        if ($user) {
            $otp = $this->otps->generate($user->email, OtpService::PASSWORD_RESET, 15);
            $this->otps->send($otp, $user);
        }

        // Store the email so the verify screen can use it
        $request->session()->put('reset_email', $request->email);

        return redirect()->route('password.otp.verify')
            ->with('status', 'If that email is registered, we have sent a 6-digit code to it.');
    }

    /** Step 2 — show the OTP + new password form. */
    public function showVerify(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password-otp', [
            'email' => $request->session()->get('reset_email'),
        ]);
    }

    /** Step 2 POST — verify OTP and set new password. */
    public function resetWithOtp(Request $request): RedirectResponse
    {
        $email = $request->session()->get('reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'code'     => ['required', 'string', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! $this->otps->verify($email, OtpService::PASSWORD_RESET, $request->code)) {
            return back()->withErrors(['code' => 'That code is incorrect or has expired.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request');
        }

        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $request->session()->forget('reset_email');

        return redirect()->route('login')
            ->with('status', 'Your password has been reset. Please log in with your new password.');
    }
}
