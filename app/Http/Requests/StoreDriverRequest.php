<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('drivers.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'pool_id' => ['nullable', 'exists:pools,id'],
            'employee_code' => ['nullable', 'string', 'max:255', 'unique:drivers,employee_code'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'license_type' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'license_expired_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,sick,on_leave'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
