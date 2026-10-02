<?php

namespace App\Http\Requests\Farmer;

use App\Enums\ProductUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isFarmer() && $this->user()->farmerProfile !== null;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'short_description' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'unit' => ['required', new Enum(ProductUnit::class)],
            'quantity_available' => ['required', 'integer', 'min:0', 'max:99999'],
            'is_available'      => ['boolean'],
            'image_url'         => ['nullable', 'url', 'max:500'],
            'image_public_id'   => ['nullable', 'string', 'max:200'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_available' => $this->boolean('is_available', true)]);
    }
}
