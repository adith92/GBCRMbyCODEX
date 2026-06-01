<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('clients.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:255', 'unique:clients,code'],
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tier' => ['required', 'in:bronze,silver,gold,platinum'],
            'industry' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,prospect'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
