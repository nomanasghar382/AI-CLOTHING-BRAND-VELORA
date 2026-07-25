<?php

namespace App\Services\Style;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class WeatherService
{
    public function current(?string $city): ?array
    {
        if (blank($city) || blank(config('services.weather.key'))) {
            return null;
        }

        try {
            $data = Http::timeout((int) config('services.weather.timeout', 8))
                ->get(rtrim(config('services.weather.url'), '/').'/weather', [
                    'q' => $city, 'appid' => config('services.weather.key'), 'units' => 'metric',
                ])->throw()->json();

            return ['city' => $data['name'] ?? $city, 'temperature_c' => $data['main']['temp'] ?? null, 'condition' => $data['weather'][0]['main'] ?? null];
        } catch (\Throwable $exception) {
            Log::notice('Weather lookup failed.', ['exception' => $exception->getMessage()]);

            return null;
        }
    }
}
