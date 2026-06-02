<?php

namespace App\Livewire\Finance\Invoices;

use App\Models\EVoucher;
use App\Models\Invoice;
use App\Services\Finance\FinanceFlowService;
use Livewire\Component;
use RuntimeException;

class Show extends Component
{
    public Invoice $invoice;

    public string $paid_at = '';
    public float $amount = 0;
    public string $method = 'bank_transfer';
    public ?string $reference_number = null;
    public ?string $notes = null;
    public ?int $e_voucher_id = null;
    public ?string $errorMessage = null;

    public function mount(Invoice $invoice): void
    {
        abort_unless(auth()->user()->can('invoices.view'), 403);

        $this->invoice = $invoice->load(['client', 'purchaseOrder.booking', 'payments.createdBy', 'payments.eVoucher']);
        $this->paid_at = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'paid_at' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'in:bank_transfer,cash,evoucher,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'e_voucher_id' => ['nullable', 'exists:e_vouchers,id'],
        ];
    }

    public function addPayment(FinanceFlowService $service): void
    {
        abort_unless(auth()->user()->can('payments.create'), 403);
        $this->errorMessage = null;

        $validated = $this->validate();

        if ($this->method === 'evoucher' && ! $this->e_voucher_id) {
            $this->addError('e_voucher_id', 'E-voucher is required for evoucher payment method.');

            return;
        }

        try {
            $service->addPayment($this->invoice, $validated, (int) auth()->id());
            $this->invoice->refresh()->load(['client', 'purchaseOrder.booking', 'payments.createdBy', 'payments.eVoucher']);
            $this->amount = 0;
            $this->reference_number = null;
            $this->notes = null;
            $this->e_voucher_id = null;
            session()->flash('success', 'Payment recorded successfully.');
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function markSent(): void
    {
        abort_unless(auth()->user()->can('invoices.update'), 403);

        if ($this->invoice->status === 'draft') {
            $this->invoice->update(['status' => 'sent', 'issued_at' => $this->invoice->issued_at ?: now()->toDateString()]);
            $this->invoice->refresh();
            session()->flash('success', 'Invoice marked as sent.');
        }
    }

    public function render()
    {
        $vouchers = EVoucher::query()
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->whereNull('expired_at')->orWhereDate('expired_at', '>=', now()->toDateString());
            })
            ->when($this->invoice->client_id, fn ($q) => $q->where(function ($i): void {
                $i->whereNull('client_id')->orWhere('client_id', $this->invoice->client_id);
            }))
            ->orderBy('code')
            ->get();

        return view('livewire.finance.invoices.show', [
            'vouchers' => $vouchers,
        ]);
    }
}
