<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const LOGIN          = 'login';
    public const PASSWORD_RESET = 'password_reset';

    /**
     * Generate a new 6-digit OTP for the given identifier and type.
     * Any previous unused OTPs of the same type are invalidated first.
     */
    public function generate(string $identifier, string $type, int $minutes = 10): Otp
    {
        // Invalidate any previous codes so only the new one is valid
        Otp::where('identifier', $identifier)
            ->where('type', $type)
            ->whereNull('used_at')
            ->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return Otp::create([
            'identifier' => $identifier,
            'type'       => $type,
            'code'       => $code,
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Send the OTP via email.
     * In local/debug environments the code is also stored in the session flash
     * so the developer can see it without an SMTP server.
     */
    public function send(Otp $otp, User $user): void
    {
        // Dev-mode hint stored in session so the view can show it
        if (app()->environment('local') || config('app.debug')) {
            session()->flash('dev_otp', $otp->code);
        }

        Mail::to($user->email)->send(new OtpMail($otp, $user));
    }

    /**
     * Verify a submitted code and mark it as used.
     * Returns true on success, false on mismatch or expiry.
     */
    public function verify(string $identifier, string $type, string $code): bool
    {
        $otp = Otp::where('identifier', $identifier)
            ->where('type', $type)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp || $otp->code !== $code) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }
}
