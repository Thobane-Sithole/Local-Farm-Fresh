<?php

namespace App\Models;

use App\Enums\Province;
use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerProfile extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'farm_name',
        'description',
        'province',
        'municipality',
        'town',
        'farm_address',
        'delivery_fee',
    ];

    protected function casts(): array
    {
        return [
            'province' => Province::class,
            'delivery_fee' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_verified' => 'boolean',
        ];
    }

    protected function slugSource(): string
    {
        return 'farm_name';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating');
    }

    /** "Polokwane, Limpopo" style label for cards. */
    public function locationLabel(): string
    {
        return collect([$this->town ?: $this->municipality, $this->province?->label()])
            ->filter()
            ->implode(', ');
    }

    /** Farmers whose account is active — suspended farmers disappear from the marketplace. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->whereHas('user', fn (Builder $q) => $q->active());
    }
}
