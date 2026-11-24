<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->fresh();
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new \InvalidArgumentException('The provided current password is incorrect.');
        }

        $user->password = $data['password'];
        $user->save();
    }

    public function updateProfilePhoto(User $user, UploadedFile $photo): User
    {
        $path = $photo->storePublicly('profile-photos', 'public');

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $path;
        $user->save();

        return $user->fresh();
    }
}
