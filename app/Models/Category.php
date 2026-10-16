<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['name', 'description', 'image_url', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function imageUrl(int $w = 600, int $h = 400): string
    {
        if ($this->image_url) {
            return $this->image_url;
        }

        $photos = [
            'fresh-fruits'       => 'photo-1519996529931-28324d5a630e',
            'vegetables'         => 'photo-1540420773420-3366772f4999',
            'herbs'              => 'photo-1466637574441-749b8f19452f',
            'eggs'               => 'photo-1498654077810-12c21d4d6dc3',
            'dairy'              => 'photo-1550583724-b2692b85b150',
            'grains-pulses'      => 'photo-1574323347407-f5e1ad6d020b',
            'meat'               => 'photo-1607623814075-e51df1bdc82f',
            'poultry'            => 'photo-1601050690597-df0568f70950',
            'homemade-products'  => 'photo-1587049352846-4a222e784d38',
            'other-farm-produce' => 'photo-1500937386664-56d1dfef3854',
        ];

        $id = $photos[$this->slug] ?? 'photo-1490818387583-1baba5e638af';

        return "https://images.unsplash.com/{$id}?w={$w}&h={$h}&fit=crop&q=80&auto=format";
    }
}
