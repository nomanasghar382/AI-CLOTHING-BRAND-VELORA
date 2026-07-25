<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_webhook_requires_valid_signature(): void
    {
        $this->postJson('/api/v1/webhooks/stripe', ['type' => 'payment_intent.succeeded', 'id' => 'evt_test'])
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_stripe_webhook_is_accepted_and_queued_when_verified(): void
    {
        Queue::fake();
        config(['velora.webhooks.stripe_secret' => 'whsec_test_secret']);
        $payload = json_encode(['type' => 'payment_intent.succeeded', 'id' => 'evt_test']);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", 'whsec_test_secret');
        $header = "t={$timestamp},v1={$signature}";

        $this->call(
            'POST',
            '/api/v1/webhooks/stripe',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Stripe-Signature' => $header],
            $payload,
        )->assertAccepted()->assertJsonPath('success', true);

        $this->assertDatabaseHas('incoming_webhook_events', [
            'provider' => 'stripe',
            'event_type' => 'payment_intent.succeeded',
            'verified' => 1,
        ]);
    }
}
