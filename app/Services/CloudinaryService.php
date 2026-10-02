<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public function isConfigured(): bool
    {
        return filled(env('CLOUDINARY_URL'));
    }

    public function uploadProductImage(UploadedFile $file): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $result = Cloudinary::uploadApi()->upload($file->getRealPath(), [
            'folder' => 'local-farm-fresh/products',
            'transformation' => [
                ['width' => 1200, 'height' => 900, 'crop' => 'limit'],
                ['quality' => 'auto', 'fetch_format' => 'auto'],
            ],
        ]);

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function uploadFarmProfileImage(UploadedFile $file): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $result = Cloudinary::uploadApi()->upload($file->getRealPath(), [
            'folder' => 'local-farm-fresh/farm-profiles',
            'transformation' => [
                ['width' => 800, 'height' => 800, 'crop' => 'fill', 'gravity' => 'face'],
                ['quality' => 'auto', 'fetch_format' => 'auto'],
            ],
        ]);

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        Cloudinary::uploadApi()->destroy($publicId);
    }
}
