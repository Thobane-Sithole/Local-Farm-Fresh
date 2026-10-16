<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CloudinarySignatureController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $url = env('CLOUDINARY_URL');

        if (! $url) {
            return response()->json(['error' => 'Cloudinary not configured'], 503);
        }

        $parsed    = parse_url($url);
        if (! is_array($parsed) || empty($parsed['user']) || empty($parsed['pass']) || empty($parsed['host'])) {
            return response()->json(['error' => 'Cloudinary URL is misconfigured on the server'], 503);
        }

        $apiKey    = $parsed['user'];
        $apiSecret = urldecode($parsed['pass']);
        $cloudName = $parsed['host'];
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
