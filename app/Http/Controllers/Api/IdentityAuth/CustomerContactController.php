<?php

namespace App\Http\Controllers\Api\IdentityAuth;

use App\Data\Responses\CustomerContactData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CustomerContactController extends Controller
{
    public function __invoke(int $customer): JsonResponse
    {
        $user = User::findOrFail($customer);

        return response()->json(CustomerContactData::fromUser($user));
    }
}
