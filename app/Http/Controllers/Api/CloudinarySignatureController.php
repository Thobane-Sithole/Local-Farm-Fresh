<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CloudinarySignatureController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Support both individual vars and the combined CLOUDINARY_URL
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        // Fall back to parsing CLOUDINARY_URL (format: cloudinary://key:secret@cloud)
        if (! $apiKey || ! $apiSecret || ! $cloudName) {
            $url = env('CLOUDINARY_URL');
            if ($url) {
                $parsed    = parse_url($url);
                $apiKey    = $apiKey    ?: ($parsed['user'] ?? null);
                $apiSecret = $apiSecret ?: urldecode($parsed['pass'] ?? '');
                $cloudName = $cloudName ?: ($parsed['host'] ?? null);
            }
        }

        if (! $apiKey || ! $apiSecret || ! $cloudName) {
            return response()->json(['error' => 'Cloudinary credentials not found. Set CLOUDINARY_URL or CLOUDINARY_API_KEY / CLOUDINARY_API_SECRET / CLOUDINARY_CLOUD_NAME on the server.'], 503);
        }

        $folder    = 'local-farm-fresh/products';
        $timestamp = (int) now()->timestamp;

        // Params must be alphabetically sorted, joined with &, then api_secret appended
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
