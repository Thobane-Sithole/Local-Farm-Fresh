<?php

namespace App\Notifications\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private readonly SmsService $sms) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $phone = $notifiable->phone ?? null;
        if (! $phone) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $this->sms->send($this->normalise($phone), $message);
    }

    private function normalise(string $phone): string
    {
        // Convert 07x or 08x South African numbers to +27 international format
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+27' . substr($digits, 1);
        }
        return '+' . ltrim($digits, '+');
    }
}
