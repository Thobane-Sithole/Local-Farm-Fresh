<?php

namespace App\Http\Requests\Farmer;

use App\Enums\ProductUnit;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $this->user()->isFarmer()
            && $this->user()->farmerProfile !== null
            && $product->farmer_profile_id === $this->user()->farmerProfile->id;
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
            'is_available' => ['boolean'],
            'image' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,png,webp'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_available' => $this->boolean('is_available')]);
    }
}
