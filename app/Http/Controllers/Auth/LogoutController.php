<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return $this->sendResponse([]);
    }
}
