<?php

namespace App\Jobs;

use App\Services\Media\CloudinaryUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessCloudinaryUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $tempPath,
        public readonly string $originalName,
        public readonly string $folder,
        public readonly int $userId,
    ) {
    }

    public function handle(CloudinaryUploadService $cloudinary): void
    {
        if (! file_exists($this->tempPath)) {
            return;
        }

        $uploaded = new UploadedFile($this->tempPath, $this->originalName, test: true);
        $cloudinary->upload($uploaded, $this->folder, $this->userId);
        @unlink($this->tempPath);
    }
}
