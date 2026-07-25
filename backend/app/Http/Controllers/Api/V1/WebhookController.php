<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIncomingWebhookJob;
use App\Services\Enterprise\WebhookService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WebhookController extends Controller
{
    use RespondsWithApi;

    public function stripe(Request $request, WebhookService $webhooks): JsonResponse
    {
        $event = $webhooks->ingest('stripe', $request, fn ($req) => $webhooks->verifyStripe($req));
        ProcessIncomingWebhookJob::dispatch($event->id);

        return $this->success(['id' => $event->id], 'Webhook received.', 202);
    }

    public function cloudinary(Request $request, WebhookService $webhooks): JsonResponse
    {
        $event = $webhooks->ingest('cloudinary', $request, fn ($req) => $webhooks->verifySharedSecret($req, 'velora.webhooks.cloudinary_secret'));
        ProcessIncomingWebhookJob::dispatch($event->id);

        return $this->success(['id' => $event->id], 'Webhook received.', 202);
    }

    public function shipping(Request $request, WebhookService $webhooks): JsonResponse
    {
        $event = $webhooks->ingest('shipping', $request, fn ($req) => $webhooks->verifySharedSecret($req, 'velora.webhooks.shipping_secret'));
        ProcessIncomingWebhookJob::dispatch($event->id);

        return $this->success(['id' => $event->id], 'Webhook received.', 202);
    }
}
