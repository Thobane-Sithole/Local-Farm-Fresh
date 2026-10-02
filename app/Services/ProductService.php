<?php

namespace App\Services;

use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(private readonly CloudinaryService $cloudinary) {}

    public function create(FarmerProfile $farmer, array $data, ?string $imageUrl, ?string $imagePublicId): Product
    {
        return DB::transaction(function () use ($farmer, $data, $imageUrl, $imagePublicId) {
            $product = $farmer->products()->create($data);

            if ($imageUrl) {
                $this->attachPreUploadedImage($product, $imageUrl, $imagePublicId);
            }

            return $product;
        });
    }

    public function update(Product $product, array $data, ?string $imageUrl, ?string $imagePublicId): Product
    {
        return DB::transaction(function () use ($product, $data, $imageUrl, $imagePublicId) {
            $product->update($data);

            if ($imageUrl) {
                $old = $product->primaryImage;
                if ($old?->public_id) {
                    $this->cloudinary->delete($old->public_id);
                }
                $product->images()->where('is_primary', true)->delete();
                $this->attachPreUploadedImage($product, $imageUrl, $imagePublicId);
            }

            return $product->fresh(['primaryImage']);
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                if ($image->public_id) {
                    $this->cloudinary->delete($image->public_id);
                }
            }
            $product->images()->delete();
            $product->delete();
        });
    }

    private function attachPreUploadedImage(Product $product, string $url, ?string $publicId): void
    {
        $product->images()->create([
            'url'        => $url,
            'public_id'  => $publicId,
            'alt_text'   => $product->name,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}
