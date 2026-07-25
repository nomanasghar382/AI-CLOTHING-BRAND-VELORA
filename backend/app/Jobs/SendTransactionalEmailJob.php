<?php

namespace App\Jobs;

use App\Services\Enterprise\StructuredLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

final class SendTransactionalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $to,
        public readonly Mailable $mailable,
    ) {
    }

    public function handle(StructuredLogService $logs): void
    {
        Mail::to($this->to)->send($this->mailable);
        $logs->queue('email.sent', ['to' => $this->to, 'mailable' => $this->mailable::class]);
    }
}
