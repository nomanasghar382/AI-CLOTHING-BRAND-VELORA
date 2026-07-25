<?php

namespace App\Services\Enterprise;

use App\Models\IncomingWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class WebhookService
{
    public function ingest(string $provider, Request $request, ?callable $verifier = null): IncomingWebhookEvent
    {
        $payload = $request->all();
        $verified = $verifier ? (bool) $verifier($request, $payload) : false;

        $event = IncomingWebhookEvent::query()->create([
            'provider' => $provider,
            'event_id' => $payload['id'] ?? $request->header('X-Request-Id'),
            'event_type' => $payload['type'] ?? $payload['event'] ?? null,
            'signature' => $request->header('Stripe-Signature')
                ?? $request->header('X-Cloudinary-Signature')
                ?? $request->header('X-Webhook-Signature'),
            'verified' => $verified,
            'status' => $verified ? 'verified' : 'received',
            'payload' => $payload,
        ]);

        Log::channel('webhooks')->info('webhook.received', [
            'provider' => $provider,
            'event_id' => $event->event_id,
            'verified' => $verified,
        ]);

        return $event;
    }

    public function verifyStripe(Request $request): bool
    {
        $secret = config('velora.webhooks.stripe_secret');
        if (! $secret) {
            return false;
        }

        $signature = $request->header('Stripe-Signature');
        if (! $signature) {
            return false;
        }

        $parts = collect(explode(',', $signature))->mapWithKeys(function (string $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

            return [$key => $value];
        });

        $signed = hash_hmac('sha256', ($parts['t'] ?? '').'.'.$request->getContent(), $secret);

        return hash_equals($signed, $parts['v1'] ?? '');
    }

    public function verifySharedSecret(Request $request, string $configKey): bool
    {
        $secret = config($configKey);
        if (! $secret) {
            return false;
        }

        $signature = $request->header('X-Webhook-Signature')
            ?? $request->header('X-Cloudinary-Signature');

        if (! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    public function markProcessed(IncomingWebhookEvent $event, ?string $error = null): void
    {
        $event->update([
            'status' => $error ? 'failed' : 'processed',
            'error' => $error,
            'processed_at' => now(),
            'attempts' => $event->attempts + 1,
        ]);
    }
}
