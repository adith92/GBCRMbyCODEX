<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('purchase-orders.view') || auth()->user()->can('invoices.view') || auth()->user()->can('payments.view') || auth()->user()->can('evouchers.view'), 403);
    }

    public function render()
    {
        $invoiceSentPartial = Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue']);

        $totalOutstanding = (float) $invoiceSentPartial->sum('total') - (float) $invoiceSentPartial->sum('paid_amount');

        return view('livewire.finance.dashboard', [
            'totalInvoiceOpen' => (float) $invoiceSentPartial->sum('total'),
            'totalPaid' => (float) Payment::query()->sum('amount'),
            'outstanding' => max(0, $totalOutstanding),
            'overdueCount' => Invoice::query()->where('status', 'overdue')->count(),
            'latestPayments' => Payment::query()->with(['invoice', 'createdBy'])->latest('paid_at')->limit(5)->get(),
            'latestInvoices' => Invoice::query()->with(['client'])->latest()->limit(5)->get(),
        ]);
    }
}
