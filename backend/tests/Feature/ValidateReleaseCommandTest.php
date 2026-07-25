<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ValidateReleaseCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_validate_release_command_runs_successfully(): void
    {
        $this->artisan('velora:validate-release')
            ->assertExitCode(0);
    }
}
