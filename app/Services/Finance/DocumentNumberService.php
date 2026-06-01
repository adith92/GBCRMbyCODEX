<?php

namespace App\Services\Finance;

use Illuminate\Database\Eloquent\Model;

class DocumentNumberService
{
    /**
     * @param class-string<Model> $modelClass
     */
    public function next(string $modelClass, string $column, string $prefix, ?string $month = null): string
    {
        $period = $month ?? now()->format('Ym');
        $fullPrefix = $prefix.'-'.$period.'-';

        $last = $modelClass::query()
            ->where($column, 'like', $fullPrefix.'%')
            ->orderByDesc($column)
            ->value($column);

        $sequence = 0;

        if (is_string($last)) {
            $parts = explode('-', $last);
            $sequence = isset($parts[2]) ? (int) $parts[2] : 0;
        }

        return $fullPrefix.str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
