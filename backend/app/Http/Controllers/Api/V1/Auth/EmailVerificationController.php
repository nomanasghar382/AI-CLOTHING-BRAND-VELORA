<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\RespondsWithApi;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class EmailVerificationController extends Controller
{
    use RespondsWithApi;

    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return $this->success(null, 'Email address verified.');
    }

    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(null, 'Email address is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->success(null, 'Verification link sent.');
    }
}
