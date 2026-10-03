@props(['account'])

<div class="sm:col-span-2">
    @if ($account->profile_image_path)
        <img src="{{ Storage::disk('public')->url($account->profile_image_path) }}" alt="Your profile photo" class="mb-3 h-16 w-16 rounded-xl object-cover">
    @endif
    <label class="block text-sm font-semibold">
        Profile photo
        <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-xl border p-3 text-sm">
    </label>
    <p class="mt-1 text-xs text-gray-500">JPG, PNG, or WEBP. Maximum 5 MB. Leave empty to keep your current photo.</p>
</div>
