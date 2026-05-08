<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->forceFill([
            'name' => $validated['name'],
        ])->save();

        return ApiResponse::success(
            data: [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
            ],
            message: 'Profile updated successfully.',
        );
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=2048,max_height=2048',
            ],
        ]);

        /** @var User $user */
        $user = $request->user();
        $path = $validated['avatar']->store('avatars', 'public');

        $user->forceFill([
            'avatar_url' => Storage::url($path),
        ])->save();

        return ApiResponse::success(
            data: [
                'avatar_url' => $user->avatar_url,
            ],
            message: 'Avatar updated successfully.',
        );
    }
}
