<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->middleware('api.version:v2')->group(function (): void {
    Route::get('status', fn () => ApiResponse::success([
        'version' => 'v2',
        'status' => 'planned',
        'message' => 'VELORA API v2 architecture endpoint. v1 remains the active production API.',
    ]));
});
