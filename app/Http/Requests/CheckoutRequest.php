<?php

namespace App\Http\Requests;

use App\Enums\Province;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name'  => ['required', 'string', 'max:120'],
            'phone'           => ['required', 'string', 'max:20'],
            'street_address'  => ['required', 'string', 'max:255'],
            'suburb'          => ['nullable', 'string', 'max:120'],
            'town'            => ['required', 'string', 'max:120'],
            'municipality'    => ['nullable', 'string', 'max:120'],
            'province'        => ['required', new Enum(Province::class)],
            'postal_code'     => ['nullable', 'string', 'max:10'],
            'delivery_notes'  => ['nullable', 'string', 'max:500'],
        ];
    }
}
