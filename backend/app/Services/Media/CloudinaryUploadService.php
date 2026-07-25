<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class CloudinaryUploadService
{
    /** @return array{url:string,public_id:string} */
    public function upload(UploadedFile $file, string $folder): array
    {
        $config = config('services.cloudinary');
        if (empty($config['cloud_name']) || empty($config['api_key']) || empty($config['api_secret'])) {
            throw new RuntimeException('Cloudinary uploads are not configured.');
        }
        $timestamp = time();
        $signature = sha1("folder={$folder}&timestamp={$timestamp}{$config['api_secret']}");
        $response = Http::timeout((int) $config['timeout'])->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$config['cloud_name']}/image/upload", ['api_key' => $config['api_key'], 'timestamp' => $timestamp, 'folder' => $folder, 'signature' => $signature]);
        if (! $response->successful()) {
            throw new RuntimeException('Cloudinary upload failed.');
        }

        return ['url' => $response->json('secure_url'), 'public_id' => $response->json('public_id')];
    }
}
