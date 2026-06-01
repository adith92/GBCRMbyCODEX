<?php

namespace App\Services;

use App\Models\Booking;

class BookingNumberService
{
    public function generate(?string $month = null): string
    {
        $period = $month ?? now()->format('Ym');
        $prefix = "BK-{$period}-";

        $lastBooking = Booking::query()
            ->where('booking_number', 'like', $prefix.'%')
            ->orderByDesc('booking_number')
            ->first();

        $lastSequence = 0;

        if ($lastBooking) {
            $parts = explode('-', $lastBooking->booking_number);
            $lastSequence = isset($parts[2]) ? (int) $parts[2] : 0;
        }

        return $prefix.str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
