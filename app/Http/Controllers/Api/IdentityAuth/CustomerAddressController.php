<?php

namespace App\Http\Controllers\Api;

use App\Data\Responses\AddressData;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;

class CustomerAddressController extends Controller
{
    public function __invoke(int $customer, int $address): JsonResponse
    {
        $address = Address::where('id', $address)->where('user_id', $customer)->firstOrFail();

        return response()->json(AddressData::fromAddress($address));
    }
}
