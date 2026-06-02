<?php

namespace App\Livewire\Admin\Hr;

use App\Models\Driver;
use App\Models\DriverAttendance;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{
    use WithPagination;

    public ?int $driver_id = null;
    public string $attendance_date = '';
    public string $status = 'present';
    public ?string $notes = null;

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('super-admin')
            && auth()->user()->can('admin.access')
            && auth()->user()->can('hr.view')
            && auth()->user()->can('hr.attendance.manage'),
            403
        );
        $this->attendance_date = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'driver_id' => ['required', 'exists:drivers,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,sick,leave'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        DriverAttendance::query()->updateOrCreate(
            [
                'driver_id' => $validated['driver_id'],
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $this->reset(['driver_id', 'notes']);
        $this->status = 'present';
        $this->attendance_date = now()->toDateString();

        session()->flash('success', 'Attendance saved successfully.');
    }

    public function editAttendance(int $attendanceId): void
    {
        $attendance = DriverAttendance::query()->findOrFail($attendanceId);
        $this->driver_id = $attendance->driver_id;
        $this->attendance_date = $attendance->attendance_date?->toDateString() ?? now()->toDateString();
        $this->status = $attendance->status;
        $this->notes = $attendance->notes;
    }

    public function render()
    {
        return view('livewire.admin.hr.attendance', [
            'drivers' => Driver::query()->orderBy('name')->get(),
            'attendanceRows' => DriverAttendance::query()->with('driver')->latest('attendance_date')->paginate(10),
            'breadcrumbs' => [
                ['label' => 'HR Attendance', 'url' => route('admin.hr.attendance')],
            ],
        ]);
    }
}
