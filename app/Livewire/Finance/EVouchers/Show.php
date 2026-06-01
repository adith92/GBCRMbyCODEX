<?php

namespace App\Livewire\Finance\EVouchers;

use App\Models\EVoucher;
use Livewire\Component;

class Show extends Component
{
    public EVoucher $eVoucher;

    public function mount(EVoucher $eVoucher): void
    {
        abort_unless(auth()->user()->can('evouchers.view'), 403);
        $this->eVoucher = $eVoucher->load(['client', 'payments.invoice']);
    }

    public function render()
    {
        return view('livewire.finance.e-vouchers.show');
    }
}
