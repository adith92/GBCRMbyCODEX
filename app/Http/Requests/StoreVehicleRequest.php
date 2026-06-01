<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('vehicles.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'pool_id' => ['nullable', 'exists:pools,id'],
            'plate_number' => ['required', 'string', 'max:255', 'unique:vehicles,plate_number'],
            'product_line' => ['required', 'in:goldenbird,bigbird,cititrans,regular'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'seat_capacity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:available,po,maintenance,hold'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
