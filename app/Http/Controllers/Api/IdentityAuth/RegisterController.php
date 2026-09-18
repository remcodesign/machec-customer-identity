<?php

namespace App\Http\Controllers\Api\IdentityAuth;

use App\Actions\IdentityAuth\RegisterAction;
use App\Data\Requests\RegisterData;
use App\Data\Responses\AuthSessionData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(Request $request, RegisterData $data, RegisterAction $action): JsonResponse
    {
        return response()->json(AuthSessionData::fromUser($action->handle($request, $data)));
    }
}
