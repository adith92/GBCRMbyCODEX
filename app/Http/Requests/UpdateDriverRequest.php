<?php

namespace App\Http\Requests;

use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('drivers.update') ?? false;
    }

    public function rules(): array
    {
        /** @var Driver $driver */
        $driver = $this->route('driver');

        return [
            'pool_id' => ['nullable', 'exists:pools,id'],
            'employee_code' => ['nullable', 'string', 'max:255', Rule::unique('drivers', 'employee_code')->ignore($driver->id)],
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
