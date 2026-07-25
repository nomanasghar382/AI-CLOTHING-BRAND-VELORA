<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLoginEvent;
use App\Models\UserDevice;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SecurityController extends Controller
{
    use RespondsWithApi;

    public function activity(Request $request): JsonResponse
    {
        return $this->success(
            SecurityLoginEvent::query()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->limit(50)
                ->get()
        );
    }

    public function devices(Request $request): JsonResponse
    {
        return $this->success(
            UserDevice::query()
                ->where('user_id', $request->user()->id)
                ->whereNull('revoked_at')
                ->latest('last_seen_at')
                ->get()
        );
    }

    public function trustDevice(Request $request, UserDevice $device): JsonResponse
    {
        abort_unless($device->user_id === $request->user()->id, 404);
        $device->update(['trusted' => true]);

        return $this->success($device, 'Device marked as trusted.');
    }

    public function revokeDevice(Request $request, UserDevice $device): JsonResponse
    {
        abort_unless($device->user_id === $request->user()->id, 404);
        $device->update(['revoked_at' => now(), 'trusted' => false, 'remembered' => false]);

        return $this->success(null, 'Device revoked.');
    }
}
