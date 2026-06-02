<?php

namespace App\Livewire\Admin\Hr;

use App\Models\Driver;
use Livewire\Component;

class Licenses extends Component
{
    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('super-admin')
            && auth()->user()->can('admin.access')
            && auth()->user()->can('hr.view')
            && auth()->user()->can('hr.licenses.manage'),
            403
        );
    }

    public function render()
    {
        return view('livewire.admin.hr.licenses', [
            'expiredDrivers' => Driver::query()
                ->whereNotNull('license_expired_at')
                ->whereDate('license_expired_at', '<', now()->toDateString())
                ->orderBy('license_expired_at')
                ->get(),
            'expiringDrivers' => Driver::query()
                ->whereNotNull('license_expired_at')
                ->whereBetween('license_expired_at', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->orderBy('license_expired_at')
                ->get(),
            'breadcrumbs' => [
                ['label' => 'HR Licenses', 'url' => route('admin.hr.licenses')],
            ],
        ]);
    }
}
