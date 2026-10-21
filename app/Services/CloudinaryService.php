<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private function sdk(): \Cloudinary\Cloudinary
    {
        return new \Cloudinary\Cloudinary();
    }

    public function isConfigured(): bool
    {
        return filled(env('CLOUDINARY_URL'));
    }

    public function uploadProductImage(UploadedFile $file): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $result = $this->sdk()->uploadApi()->upload($file->getRealPath(), [
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

        $result = $this->sdk()->uploadApi()->upload($file->getRealPath(), [
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

        try {
            $this->sdk()->uploadApi()->destroy($publicId);
        } catch (\Throwable) {
            // Do not let a Cloudinary API failure block database operations.
        }
    }
}
