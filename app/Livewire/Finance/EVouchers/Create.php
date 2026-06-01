<?php

namespace App\Livewire\Finance\EVouchers;

use App\Models\Client;
use App\Models\EVoucher;
use App\Services\Finance\DocumentNumberService;
use Livewire\Component;

class Create extends Component
{
    public ?int $client_id = null;
    public float $amount = 0;
    public float $used_amount = 0;
    public ?string $expired_at = null;
    public string $status = 'active';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('evouchers.create'), 403);
    }

    protected function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'used_amount' => ['required', 'numeric', 'min:0'],
            'expired_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,used,expired,cancelled'],
        ];
    }

    public function save(DocumentNumberService $numbers)
    {
        $validated = $this->validate();

        $voucher = EVoucher::query()->create([
            ...$validated,
            'code' => $numbers->next(EVoucher::class, 'code', 'EV'),
            'used_at' => $validated['status'] === 'used' ? now() : null,
        ]);

        session()->flash('success', 'E-voucher created successfully.');

        return redirect()->route('finance.e-vouchers.show', $voucher);
    }

    public function render()
    {
        return view('livewire.finance.e-vouchers.create', [
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }
}
