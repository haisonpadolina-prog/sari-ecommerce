<?php

namespace App\Services\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountProfileService
{
    public function save(Model $account, array $attributes, Request $request): void
    {
        $newPath = null;
        $oldPath = (string) ($account->getAttribute('profile_image_path') ?? '');
        $removePhoto = $request->boolean('remove_profile_image');

        if ($request->hasFile('profile_image')) {
            $newPath = $request->file('profile_image')->store(
                'account-profiles/'.$account->getTable().'/'.$account->getKey(),
                'public'
            );

            if (!$newPath) {
                throw ValidationException::withMessages([
                    'profile_image' => 'The profile photo could not be saved. Please try again.',
                ]);
            }

            $attributes['profile_image_path'] = $newPath;
        } elseif ($removePhoto) {
            $attributes['profile_image_path'] = null;
        }

        try {
            $account->forceFill($attributes)->save();
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        if (
            $oldPath !== ''
            && str_starts_with($oldPath, 'account-profiles/')
            && ($newPath !== null || $removePhoto)
            && $oldPath !== $newPath
        ) {
            /*
             * Registration profile photos are shared with the immutable
             * registration/compliance record. Only delete photos that were
             * later uploaded from an account-management screen.
             */
            Storage::disk('public')->delete($oldPath);
        }
    }
}
