<?php

namespace App\Services;

use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(private readonly CloudinaryService $cloudinary) {}

    public function create(FarmerProfile $farmer, array $data, ?UploadedFile $image): Product
    {
        return DB::transaction(function () use ($farmer, $data, $image) {
            $product = $farmer->products()->create($data);

            if ($image) {
                $this->attachPrimaryImage($product, $image);
            }

            return $product;
        });
    }

    public function update(Product $product, array $data, ?UploadedFile $image): Product
    {
        return DB::transaction(function () use ($product, $data, $image) {
            $product->update($data);

            if ($image) {
                $old = $product->primaryImage;
                if ($old?->public_id) {
                    $this->cloudinary->delete($old->public_id);
                }
                $product->images()->where('is_primary', true)->delete();
                $this->attachPrimaryImage($product, $image);
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

    private function attachPrimaryImage(Product $product, UploadedFile $file): void
    {
        $uploaded = $this->cloudinary->uploadProductImage($file);

        if ($uploaded === null) {
            return;
        }

        $product->images()->create([
            'url' => $uploaded['url'],
            'public_id' => $uploaded['public_id'],
            'alt_text' => $product->name,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}
