<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('clients.update') ?? false;
    }

    public function rules(): array
    {
        /** @var Client $client */
        $client = $this->route('client');

        return [
            'code' => ['nullable', 'string', 'max:255', Rule::unique('clients', 'code')->ignore($client->id)],
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
