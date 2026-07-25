<?php

namespace App\Jobs;

use App\Models\IncomingWebhookEvent;
use App\Services\Enterprise\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessIncomingWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $webhookEventId)
    {
    }

    public function handle(WebhookService $webhooks): void
    {
        $event = IncomingWebhookEvent::query()->findOrFail($this->webhookEventId);
        try {
            // Provider-specific routing can be extended here.
            $webhooks->markProcessed($event);
        } catch (\Throwable $exception) {
            $webhooks->markProcessed($event, $exception->getMessage());
            throw $exception;
        }
    }
}
