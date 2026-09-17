<?php

namespace App\Http\Controllers\Api;

use App\Actions\LoginAction;
use App\Concerns\BuildsAuthSessionResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use BuildsAuthSessionResponse;

    public function __invoke(Request $request, LoginAction $action): JsonResponse
    {
        return $this->sessionResponse($action->handle($request));
    }
}
