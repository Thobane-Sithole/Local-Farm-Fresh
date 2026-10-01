<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = filled(config('services.africastalking.api_key'))
            && filled(config('services.africastalking.username'));
    }

    public function send(string $to, string $message): void
    {
        if (! $this->enabled) {
            Log::info("SMS [disabled]: to={$to} | {$message}");
            return;
        }

        $username = config('services.africastalking.username');
        $apiKey   = config('services.africastalking.api_key');
        $baseUrl  = $username === 'sandbox'
            ? 'https://api.sandbox.africastalking.com/version1'
            : 'https://api.africastalking.com/version1';

        Http::withHeaders(['apiKey' => $apiKey, 'Accept' => 'application/json'])
            ->asForm()
            ->post("{$baseUrl}/messaging", [
                'username'    => $username,
                'to'          => $to,
                'message'     => $message,
                'from'        => 'LFF',
            ]);
    }
}
