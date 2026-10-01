<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Fresh food from local farmers.');
    }
}
