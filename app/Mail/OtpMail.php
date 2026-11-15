<?php

namespace App\Mail;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Otp  $otp,
        public readonly User $user,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->otp->type) {
            'login'          => 'Your login verification code',
            'password_reset' => 'Your password reset code',
            default          => 'Your verification code',
        };

        return new Envelope(subject: $subject . ' — Local-Farm-Fresh');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.otp');
    }
}
