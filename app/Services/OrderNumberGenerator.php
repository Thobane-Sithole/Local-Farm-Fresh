<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Produces LFF-YYYYMMDD-NNNN numbers.
 *
 * Uses a single atomic upsert ("INSERT ... ON CONFLICT DO UPDATE ... RETURNING"),
 * so two customers checking out in the same millisecond can never receive the
 * same number. Works on PostgreSQL and SQLite 3.35+ (used by the test suite).
 */
class OrderNumberGenerator
{
    public const PREFIX = 'LFF';

    public function next(?Carbon $date = null): string
    {
        $date = ($date ?? now())->copy()->timezone(config('app.timezone'));

        $row = DB::selectOne(
            'insert into order_number_sequences (sequence_date, last_number) values (?, 1)
             on conflict (sequence_date) do update
             set last_number = order_number_sequences.last_number + 1
             returning last_number',
            [$date->toDateString()]
        );

        return sprintf(
            '%s-%s-%s',
            self::PREFIX,
            $date->format('Ymd'),
            str_pad((string) $row->last_number, 4, '0', STR_PAD_LEFT),
        );
    }
}
