<x-layouts.app :title="'HR Attendance'" :header="'HR / Attendance'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="rounded-lg border bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-4">
            <select wire:model="driver_id" class="rounded border-slate-300 text-sm">
                <option value="">Select driver</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                @endforeach
            </select>
            <input wire:model="attendance_date" type="date" class="rounded border-slate-300 text-sm">
            <select wire:model="status" class="rounded border-slate-300 text-sm">
                @foreach(['present', 'absent', 'sick', 'leave'] as $status)
                    <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                @endforeach
            </select>
            <input wire:model="notes" placeholder="Notes" class="rounded border-slate-300 text-sm">
            <div class="md:col-span-4 text-right">
                <button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save Attendance</button>
            </div>
        </form>
    </section>

    <section class="rounded-lg border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Driver</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Notes</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($attendanceRows as $row)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $row->driver?->name }}</td>
                        <td class="px-4 py-3">{{ $row->attendance_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 uppercase text-xs">{{ $row->status }}</td>
                        <td class="px-4 py-3">{{ $row->notes ?: '-' }}</td>
                        <td class="px-4 py-3 text-right"><button wire:click="editAttendance({{ $row->id }})" class="text-blue-600">Edit</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $attendanceRows->links() }}</div>
    </section>
</x-layouts.app>
