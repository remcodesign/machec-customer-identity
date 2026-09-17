<?php

namespace App\Http\Controllers\Api;

use App\Actions\RegisterAction;
use App\Concerns\BuildsAuthSessionResponse;
use App\Data\Requests\RegisterData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use BuildsAuthSessionResponse;

    public function __invoke(Request $request, RegisterData $data, RegisterAction $action): JsonResponse
    {
        return $this->sessionResponse($action->handle($request, $data));
    }
}
