<?php

namespace App\Http\Controllers\Api\IdentityAuth;

use App\Actions\IdentityAuth\PasswordResetRequestAction;
use App\Data\Requests\PasswordResetRequestData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class PasswordResetRequestController extends Controller
{
    public function __invoke(PasswordResetRequestData $data, PasswordResetRequestAction $action): Response
    {
        $action->handle($data);

        return response()->noContent(202);
    }
}
