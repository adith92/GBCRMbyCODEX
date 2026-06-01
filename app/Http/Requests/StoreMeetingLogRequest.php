<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('meeting-logs.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'meeting_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'outcome' => ['required', 'in:prospecting,follow_up,negotiation,closed_won,closed_lost'],
            'notes' => ['nullable', 'string'],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }
}
