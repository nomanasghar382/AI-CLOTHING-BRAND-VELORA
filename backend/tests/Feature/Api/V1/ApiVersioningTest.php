<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class ApiVersioningTest extends TestCase
{
    public function test_v2_status_endpoint_is_available(): void
    {
        $this->getJson('/api/v2/status', ['X-Api-Version' => 'v2'])
            ->assertOk()
            ->assertJsonPath('data.version', 'v2')
            ->assertHeader('X-Api-Version', 'v2');
    }
}
