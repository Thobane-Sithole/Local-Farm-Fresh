<?php

namespace App\Http\Requests\Auth;

use App\Enums\Province;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class FarmerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            // Farmers type numbers many ways: "072 123 4567", "072-123-4567".
            'phone' => preg_replace('/[\s\-()]/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['required', 'regex:/^(\+27|0)[1-8][0-9]{8}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'farm_name' => ['required', 'string', 'max:120'],
            'province' => ['required', Rule::enum(Province::class)],
            'municipality' => ['required', 'string', 'max:120'],
            'town' => ['nullable', 'string', 'max:120'],
            'farm_address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a South African phone number, like 072 123 4567.',
            'farm_name.required' => 'Tell customers the name of your farm or business.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'farm_name' => 'farm name',
            'farm_address' => 'farm address',
        ];
    }
}
