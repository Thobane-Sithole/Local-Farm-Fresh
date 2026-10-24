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
            'province'    => Province::class,
            'delivery_fee' => 'decimal:2',
            'latitude'    => 'decimal:7',
            'longitude'   => 'decimal:7',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public const VERIFICATION_DAYS = 30;

    /** Days remaining in the current verification period. Null if no period started. */
    public function daysRemaining(): ?int
    {
        if (! $this->verified_at) {
            return null;
        }

        $days = (int) now()->diffInDays($this->verified_at->copy()->addDays(self::VERIFICATION_DAYS), false);

        return max($days, 0);
    }

    public function isExpired(): bool
    {
        if (! $this->verified_at) {
            return false;
        }

        return $this->verified_at->copy()->addDays(self::VERIFICATION_DAYS)->isPast();
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

    /** Farmers whose account is active and verification has not expired. */
    public function scopeListed(Builder $query): Builder
    {
        return $query
            ->whereHas('user', fn (Builder $q) => $q->active())
            ->where('is_verified', true)
            ->where(fn (Builder $q) => $q
                ->whereNull('verified_at') // pre-feature farmers: no expiry
                ->orWhere('verified_at', '>=', now()->subDays(self::VERIFICATION_DAYS))
            );
    }
}
