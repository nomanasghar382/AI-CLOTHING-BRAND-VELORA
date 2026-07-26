<?php

namespace App\Services\Shopping;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final class StripePaymentService
{
    public function createIntent(Order $order): array
    {
        $secret = (string) config('services.stripe.secret');
        $payment = $order->payments()->where('provider', 'stripe')->latest()->first();

        if ($secret === '') {
            return ['available' => false, 'reason' => 'Stripe payments are not configured.'];
        }

        if ($payment?->provider_reference) {
            return ['available' => true, 'payment' => $payment, 'client_secret' => $payment->payload['client_secret'] ?? null];
        }

        try {
            $intent = (new StripeClient($secret))->paymentIntents->create([
                'amount' => (int) round((float) $order->grand_total * 100),
                'currency' => strtolower($order->currency),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => ['order_id' => (string) $order->id, 'order_number' => $order->number],
            ], ['idempotency_key' => "velora-order-{$order->id}"]);
        } catch (ApiErrorException $exception) {
            report($exception);

            return ['available' => false, 'reason' => 'Stripe is temporarily unavailable.'];
        }

        $payment ??= Payment::query()->create([
            'order_id' => $order->id, 'provider' => 'stripe', 'status' => 'pending',
            'amount' => $order->grand_total, 'currency' => $order->currency,
        ]);
        $payment->update([
            'provider_reference' => $intent->id,
            'payload' => ['client_secret' => $intent->client_secret],
        ]);
        PaymentTransaction::query()->firstOrCreate(
            ['payment_id' => $payment->id, 'provider_reference' => $intent->id, 'type' => 'payment_intent'],
            ['status' => $intent->status, 'amount' => $order->grand_total, 'payload' => ['client_secret' => $intent->client_secret]],
        );

        return ['available' => true, 'payment' => $payment->fresh(), 'client_secret' => $intent->client_secret];
    }
}
