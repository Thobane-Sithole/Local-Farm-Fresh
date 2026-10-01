<?php

namespace Tests\Feature;

use App\Services\OrderNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OrderNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_numbers_are_sequential_per_day_and_reset_daily(): void
    {
        $generator = new OrderNumberGenerator;
        $day = Carbon::parse('2026-09-30 10:00');

        $this->assertSame('LFF-20260930-0001', $generator->next($day));
        $this->assertSame('LFF-20260930-0002', $generator->next($day));
        $this->assertSame('LFF-20261001-0001', $generator->next($day->copy()->addDay()));
        $this->assertSame('LFF-20260930-0003', $generator->next($day));
    }
}
