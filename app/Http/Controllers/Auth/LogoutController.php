<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogoutController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        DeviceToken::query()->where('token', $request->device_token)->delete();
        $request->user()->token()->revoke();
        return $this->sendResponse([]);
    }
}
