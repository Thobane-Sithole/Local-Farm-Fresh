<?php

namespace App\Models;

use App\Enums\ProductUnit;
use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUniqueSlug, SoftDeletes;

    /** farmer_profile_id is set from the authenticated farmer, never from input. */
    protected $fillable = [
        'category_id',
        'name',
        'short_description',
        'description',
        'price',
        'unit',
        'quantity_available',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'unit' => ProductUnit::class,
            'quantity_available' => 'integer',
            'is_available' => 'boolean',
            'removed_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        // ProductService guarantees exactly one primary image per product.
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /** "R18.00 / kg" */
    public function priceLabel(): string
    {
        return 'R'.number_format((float) $this->price, 2).' / '.$this->unit->label();
    }

    public function isInStock(): bool
    {
        return $this->is_available && $this->removed_at === null && $this->quantity_available > 0;
    }

    /**
     * What customers can see: farmer switched it on, admin hasn't removed it,
     * and the farmer's account isn't suspended.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('is_available', true)
            ->whereNull('removed_at')
            ->whereHas('farmerProfile', fn (Builder $q) => $q->listed());
    }

    public function scopeOwnedBy(Builder $query, FarmerProfile $farmer): Builder
    {
        return $query->where('farmer_profile_id', $farmer->id);
    }
}
