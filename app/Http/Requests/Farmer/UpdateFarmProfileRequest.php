<?php

namespace App\Http\Requests\Farmer;

use App\Enums\Province;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateFarmProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isFarmer() && $this->user()->farmerProfile !== null;
    }

    public function rules(): array
    {
        return [
            'farm_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'province' => ['required', new Enum(Province::class)],
            'municipality' => ['required', 'string', 'max:120'],
            'town' => ['nullable', 'string', 'max:120'],
            'farm_address' => ['nullable', 'string', 'max:255'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'profile_image' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,png,webp'],
        ];
    }
}
