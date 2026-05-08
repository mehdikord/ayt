<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserDiscountResource;
use App\Models\User;
use App\Support\ApiResponse;

class DiscountController extends Controller
{
    public function myDiscounts()
    {
        /** @var User $user */
        $user = request()->user();

        $discounts = $user->discounts()
            ->orderByDesc('id')
            ->get();

        return ApiResponse::success(
            data: UserDiscountResource::collection($discounts),
            message: 'User discounts fetched successfully.'
        );
    }
}
