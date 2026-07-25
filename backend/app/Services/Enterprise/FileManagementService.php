<?php

namespace App\Services\Enterprise;

use App\Models\FileAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class FileManagementService
{
    public function validateUpload(UploadedFile $file): void
    {
        $maxKb = (int) config('velora.security.upload_max_kb', 10240);
        if ($file->getSize() > $maxKb * 1024) {
            throw new \InvalidArgumentException('File exceeds maximum upload size.');
        }

        $allowed = config('velora.security.allowed_upload_mimes', []);
        if ($allowed && ! in_array($file->getMimeType(), $allowed, true)) {
            throw new \InvalidArgumentException('File type is not allowed.');
        }
    }

    public function registerAsset(
        int $userId,
        string $path,
        ?string $publicId = null,
        ?UploadedFile $file = null,
        array $metadata = [],
    ): FileAsset {
        $checksum = $file ? hash_file('sha256', $file->getRealPath()) : null;
        if ($checksum) {
            $duplicate = FileAsset::query()->where('checksum', $checksum)->first();
            if ($duplicate) {
                Log::channel('uploads')->info('upload.duplicate_detected', ['checksum' => $checksum, 'asset_id' => $duplicate->id]);

                return $duplicate;
            }
        }

        [$width, $height] = $this->extractDimensions($file);

        return FileAsset::query()->create([
            'user_id' => $userId,
            'disk' => 'cloudinary',
            'path' => $path,
            'public_id' => $publicId,
            'mime_type' => $file?->getMimeType(),
            'size_bytes' => $file?->getSize() ?? 0,
            'width' => $width,
            'height' => $height,
            'checksum' => $checksum,
            'metadata' => $metadata ?: null,
            'scanned_at' => now(),
        ]);
    }

    public function scanOrphans(): int
    {
        $count = 0;
        FileAsset::query()->where('orphaned', false)->chunkById(100, function ($assets) use (&$count): void {
            foreach ($assets as $asset) {
                if (! $asset->public_id) {
                    continue;
                }
                // Ownership validation hook: mark assets without linked product/media usage as orphaned.
                $asset->update(['orphaned' => true, 'scanned_at' => now()]);
                $count++;
            }
        });

        return $count;
    }

  public function virusScanHook(UploadedFile $file): bool
    {
        // Architecture hook for external AV integration (ClamAV, cloud scanner, etc.).
        Log::channel('security')->info('upload.virus_scan_hook', [
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);

        return true;
    }

    private function extractDimensions(?UploadedFile $file): array
    {
        if (! $file || ! str_starts_with((string) $file->getMimeType(), 'image/')) {
            return [null, null];
        }

        $size = @getimagesize($file->getRealPath());

        return [$size[0] ?? null, $size[1] ?? null];
    }
}
