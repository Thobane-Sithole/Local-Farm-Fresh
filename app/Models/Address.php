<?php

namespace App\Models;

use App\Enums\Province;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'recipient_name', 'phone', 'street_address', 'suburb',
        'town', 'municipality', 'province', 'postal_code', 'delivery_notes', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'province' => Province::class,
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function singleLine(): string
    {
        return collect([$this->street_address, $this->suburb, $this->town, $this->postal_code])
            ->filter()->implode(', ');
    }
}
