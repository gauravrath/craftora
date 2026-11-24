<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateProfilePhotoRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UserResource::make($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => UserResource::make($user),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userService->changePassword($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    public function updatePhoto(UpdateProfilePhotoRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfilePhoto($request->user(), $request->file('photo'));

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully.',
            'data' => UserResource::make($user),
        ]);
    }
}
