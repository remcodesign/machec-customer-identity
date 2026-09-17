<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Http\JsonResponse;

trait BuildsAuthSessionResponse
{
    private function sessionResponse(User $user): JsonResponse
    {
        return response()->json([
            'customer_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
