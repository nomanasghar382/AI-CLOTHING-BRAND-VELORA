<?php

namespace App\Services\Style;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AiFashionService
{
    /**
     * @param  array<int, array{id:int,name:string,category:?string,price:float}>  $catalog
     * @param  array<string, mixed>  $context
     * @return array{provider:string,reply:string,items:array<int, array{product_id:int,reason:string,score:float}>}
     */
    public function recommend(array $catalog, array $context): array
    {
        if (blank(config('services.openai.key'))) {
            return $this->fallback($catalog, $context);
        }

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are VELORA’s fashion assistant. Recommend ONLY product IDs from the supplied catalog. Return concise JSON.'],
                ['role' => 'user', 'content' => json_encode(['context' => $context, 'catalog' => $catalog], JSON_THROW_ON_ERROR)],
            ],
            'response_format' => ['type' => 'json_schema', 'json_schema' => [
                'name' => 'fashion_recommendation', 'strict' => true,
                'schema' => ['type' => 'object', 'properties' => [
                    'reply' => ['type' => 'string'],
                    'items' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => [
                        'product_id' => ['type' => 'integer'], 'reason' => ['type' => 'string'], 'score' => ['type' => 'number'],
                    ], 'required' => ['product_id', 'reason', 'score'], 'additionalProperties' => false]],
                ], 'required' => ['reply', 'items'], 'additionalProperties' => false],
            ]],
        ];

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->acceptJson()->timeout((int) config('services.openai.timeout', 15))
                ->retry(2, 500, fn ($exception) => $exception instanceof RequestException)
                ->post(rtrim(config('services.openai.url', 'https://api.openai.com/v1'), '/').'/chat/completions', $payload)
                ->throw();
            $data = json_decode($response->json('choices.0.message.content', '{}'), true, 512, JSON_THROW_ON_ERROR);
            $allowed = array_flip(array_column($catalog, 'id'));
            $items = array_values(array_filter($data['items'] ?? [], fn ($item) => isset($allowed[$item['product_id'] ?? 0])));

            return ['provider' => 'openai', 'reply' => (string) ($data['reply'] ?? ''), 'items' => array_slice($items, 0, 8)];
        } catch (\Throwable $exception) {
            Log::warning('AI fashion request failed; using catalog fallback.', ['exception' => $exception->getMessage()]);

            return $this->fallback($catalog, $context);
        }
    }

    private function fallback(array $catalog, array $context): array
    {
        $budget = isset($context['budget']) ? (float) $context['budget'] : null;
        $items = array_values(array_filter($catalog, fn ($product) => $budget === null || $product['price'] <= $budget));
        $items = array_slice($items ?: $catalog, 0, 4);

        return [
            'provider' => 'catalog_fallback',
            'reply' => $items ? 'Here are available catalog pieces selected for your preferences.' : 'No active, available products match this request.',
            'items' => array_map(fn ($product, $index) => ['product_id' => $product['id'], 'reason' => 'Available catalog match', 'score' => round(1 - ($index * .1), 2)], $items, array_keys($items)),
        ];
    }
}
