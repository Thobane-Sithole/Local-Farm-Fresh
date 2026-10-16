<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CloudinarySignatureController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Use the Cloudinary SDK's own config reader — handles every URL format correctly
        $cloudName = $apiKey = $apiSecret = null;

        try {
            $cld       = new \Cloudinary\Cloudinary();
            $cloudName = (string) $cld->configuration->cloud->cloudName;
            $apiKey    = (string) $cld->configuration->cloud->apiKey;
            $apiSecret = (string) $cld->configuration->cloud->apiSecret;
        } catch (\Throwable) {
            // SDK couldn't read config — fall through to individual env vars
        }

        // Also accept individual env vars in case SDK config is empty
        $apiKey    = ($apiKey    ?: env('CLOUDINARY_API_KEY'))    ?: null;
        $apiSecret = ($apiSecret ?: env('CLOUDINARY_API_SECRET')) ?: null;
        $cloudName = ($cloudName ?: env('CLOUDINARY_CLOUD_NAME')) ?: null;

        if (! $apiKey || ! $apiSecret || ! $cloudName) {
            return response()->json([
                'error' => 'Cloudinary not configured. Add CLOUDINARY_URL (or CLOUDINARY_API_KEY + CLOUDINARY_API_SECRET + CLOUDINARY_CLOUD_NAME) to your Render environment variables.',
            ], 503);
        }

        $folder    = 'local-farm-fresh/products';
        $timestamp = (int) now()->timestamp;

        // Params alphabetically sorted + api_secret appended (Cloudinary signed-upload spec)
        $toSign    = "folder={$folder}&timestamp={$timestamp}";
        $signature = sha1($toSign . $apiSecret);

        return response()->json([
            'signature'  => $signature,
            'timestamp'  => $timestamp,
            'api_key'    => $apiKey,
            'cloud_name' => $cloudName,
            'folder'     => $folder,
        ]);
    }
}
