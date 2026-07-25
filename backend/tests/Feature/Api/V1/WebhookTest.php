<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_webhook_is_accepted_and_queued(): void
    {
        $this->postJson('/api/v1/webhooks/stripe', ['type' => 'payment_intent.succeeded', 'id' => 'evt_test'])
            ->assertAccepted()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('incoming_webhook_events', [
            'provider' => 'stripe',
            'event_type' => 'payment_intent.succeeded',
        ]);
    }
}
