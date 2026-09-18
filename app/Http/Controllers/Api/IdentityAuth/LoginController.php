<?php

namespace App\Http\Controllers\Api\IdentityAuth;

use App\Actions\IdentityAuth\LoginAction;
use App\Data\Requests\LoginData;
use App\Data\Responses\AuthSessionData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(Request $request, LoginData $data, LoginAction $action): JsonResponse
    {
        return response()->json(AuthSessionData::fromUser($action->handle($request, $data)));
    }
}
