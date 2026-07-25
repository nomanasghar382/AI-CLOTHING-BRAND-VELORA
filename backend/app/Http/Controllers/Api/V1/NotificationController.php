<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    use RespondsWithApi;

    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->limit(50)->get()->map(fn ($notification) => [
            'id' => $notification->id,
            'title' => $notification->data['title'] ?? 'VELORA update',
            'body' => $notification->data['body'] ?? $notification->data['message'] ?? '',
            'type' => class_basename($notification->type),
            'read' => $notification->read_at !== null,
            'created_at' => $notification->created_at?->toIso8601String(),
            'data' => $notification->data,
        ]);

        return $this->success($notifications);
    }

    public function update(Request $request, string $notification): JsonResponse
    {
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        if ($request->boolean('read') && ! $record->read_at) {
            $record->markAsRead();
        }

        return $this->success(null, 'Notification updated.');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->success(null, 'All notifications marked as read.');
    }
}
