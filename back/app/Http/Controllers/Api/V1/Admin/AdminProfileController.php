<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        /** @var Admin $admin */
        $admin = $request->user();
        $admin->forceFill(['name' => $validated['name']])->save();

        return ApiResponse::success(
            data: [
                'id' => $admin->id,
                'name' => $admin->name,
                'phone' => $admin->phone,
                'image' => $admin->image,
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

        /** @var Admin $admin */
        $admin = $request->user();
        $path = $validated['avatar']->store('avatars', 'public');

        $admin->forceFill([
            'image' => Storage::url($path),
        ])->save();

        return ApiResponse::success(
            data: [
                'image' => $admin->image,
            ],
            message: 'Avatar updated successfully.',
        );
    }
}
