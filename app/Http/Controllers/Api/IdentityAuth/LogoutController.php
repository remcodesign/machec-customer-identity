<?php

namespace App\Http\Controllers\Api\IdentityAuth;

use App\Actions\IdentityAuth\LogoutAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutAction $action): Response
    {
        $action->handle($request);

        return response()->noContent();
    }
}
