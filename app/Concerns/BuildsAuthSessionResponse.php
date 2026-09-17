<?php

namespace App\Concerns;

use App\Data\Responses\AuthSessionData;
use App\Models\User;
use Illuminate\Http\JsonResponse;

trait BuildsAuthSessionResponse
{
    private function sessionResponse(User $user): JsonResponse
    {
        return response()->json(AuthSessionData::fromUser($user));
    }
}
