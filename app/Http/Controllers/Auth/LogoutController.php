<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        DeviceToken::query()->where('token', $request->user()->device_token)->delete();
        $request->user()->token()->revoke();
        return $this->sendResponse([]);
    }
}
