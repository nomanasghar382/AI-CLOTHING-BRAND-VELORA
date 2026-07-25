<?php

namespace App\Jobs;

use App\Models\ReportExport;
use App\Services\Enterprise\StructuredLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

final class GenerateReportExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $exportId)
    {
    }

    public function handle(StructuredLogService $logs): void
    {
        $export = ReportExport::query()->findOrFail($this->exportId);
        $path = 'exports/'.$export->report_type.'-'.$export->id.'.csv';
        Storage::disk('local')->put($path, "id,generated_at\n{$export->id},".now()->toIso8601String());
        $export->update(['generated_at' => now(), 'row_count' => 1]);
        $logs->queue('report.export.completed', ['export_id' => $export->id, 'report' => $export->report_type]);
    }
}
