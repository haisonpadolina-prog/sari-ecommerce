<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform\PlatformAnnouncement;
use App\Services\Platform\PlatformAnnouncementService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AdminPlatformAnnouncementController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $announcements = PlatformAnnouncement::query()
            ->with('admin:id,name,email')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $editAnnouncement = null;
        $editId = (int) $request->query('edit', 0);

        if ($editId > 0) {
            $editAnnouncement = PlatformAnnouncement::query()->findOrFail($editId);
        }

        return view('admin.platform-announcements', [
            'announcements' => $announcements,
            'editAnnouncement' => $editAnnouncement,
            'audienceOptions' => PlatformAnnouncementService::AUDIENCES,
            'philippineNow' => now('Asia/Manila'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->guard($request);

        $validated = $this->validated($request);
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request);
            }

            $payload = $this->payload($request, $validated);
            $payload['image_path'] = $newImagePath;

            $announcement = PlatformAnnouncement::query()->create($payload);
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.announcements', ['edit' => $announcement->id])
            ->with('success', 'Announcement saved.');
    }

    public function update(
        Request $request,
        PlatformAnnouncement $announcement
    ): RedirectResponse {
        $this->guard($request);

        $validated = $this->validated($request);

        $oldImagePath = (string) ($announcement->image_path ?? '');
        $newImagePath = null;
        $removeImage = $request->boolean('remove_image');

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request);
            }

            $payload = $this->payload($request, $validated, $announcement);

            if ($newImagePath !== null) {
                $payload['image_path'] = $newImagePath;
            } elseif ($removeImage) {
                $payload['image_path'] = null;
            }

            $announcement->fill($payload)->save();
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (
            $oldImagePath !== ''
            && ($newImagePath !== null || $removeImage)
            && $oldImagePath !== $newImagePath
        ) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()
            ->route('admin.announcements', ['edit' => $announcement->id])
            ->with('success', 'Announcement updated.');
    }

    public function publish(
        Request $request,
        PlatformAnnouncement $announcement
    ): RedirectResponse {
        $this->guard($request);

        $announcement->forceFill([
            'status' => 'published',
            'starts_at' => $announcement->starts_at ?: now(),
            'published_at' => now(),
            'paused_at' => null,
        ])->save();

        return back()->with(
            'success',
            $announcement->starts_at?->isFuture()
                ? 'Announcement scheduled.'
                : 'Announcement published.'
        );
    }

    public function pause(
        Request $request,
        PlatformAnnouncement $announcement
    ): RedirectResponse {
        $this->guard($request);

        $announcement->forceFill([
            'status' => 'paused',
            'paused_at' => now(),
        ])->save();

        return back()->with('success', 'Announcement paused.');
    }

    public function destroy(
        Request $request,
        PlatformAnnouncement $announcement
    ): RedirectResponse {
        $this->guard($request);

        $imagePath = (string) ($announcement->image_path ?? '');
        $announcement->delete();

        if ($imagePath !== '') {
            $this->deleteImage($imagePath);
        }

        return redirect()
            ->route('admin.announcements')
            ->with('success', 'Announcement deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:3000'],
            'audiences' => ['required', 'array', 'min:1'],
            'audiences.*' => [
                'required',
                'string',
                Rule::in(PlatformAnnouncementService::AUDIENCES),
            ],
            'severity' => [
                'required',
                Rule::in(['info', 'warning', 'critical']),
            ],
            'action_label' => ['nullable', 'string', 'max:80'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:max_width=5000,max_height=5000',
            ],
            'remove_image' => ['nullable', 'boolean'],
            'save_mode' => ['nullable', Rule::in(['publish', 'draft', 'keep'])],
        ]);
    }

    private function payload(Request $request, array $validated, ?PlatformAnnouncement $existing = null): array
    {
        $startsAt = !empty($validated['starts_at'])
            ? $this->manilaToUtc((string) $validated['starts_at'])
            : null;

        $endsAt = !empty($validated['ends_at'])
            ? $this->manilaToUtc((string) $validated['ends_at'])
            : null;

        if ($startsAt && $endsAt && $endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'ends_at' => 'The announcement end time must be after the start time.',
            ]);
        }

        $actionUrl = trim((string) ($validated['action_url'] ?? ''));

        if (
            $actionUrl !== ''
            && !str_starts_with($actionUrl, '/')
            && !preg_match('/^https?:\/\//i', $actionUrl)
        ) {
            throw ValidationException::withMessages([
                'action_url' => 'Use an internal path beginning with / or a valid http/https URL.',
            ]);
        }

        $saveMode = (string) ($validated['save_mode'] ?? ($existing ? 'keep' : 'publish'));

        $status = $existing?->status ?? 'draft';
        $publishedAt = $existing?->published_at;
        $pausedAt = $existing?->paused_at;
        $effectiveStartsAt = $startsAt;

        if ($saveMode === 'publish') {
            $status = 'published';
            $effectiveStartsAt = $startsAt ?: now();
            $publishedAt = $publishedAt ?: now();
            $pausedAt = null;
        } elseif ($saveMode === 'draft') {
            $status = 'draft';
            $publishedAt = null;
            $pausedAt = null;
        } elseif (!$existing) {
            $status = 'published';
            $effectiveStartsAt = $startsAt ?: now();
            $publishedAt = now();
            $pausedAt = null;
        }

        return [
            'admin_account_id' => $request->session()->get('admin_account_id')
                ? (int) $request->session()->get('admin_account_id')
                : null,
            'title' => trim((string) $validated['title']),
            'message' => trim((string) $validated['message']),
            'audiences' => array_values(array_unique($validated['audiences'])),
            'severity' => (string) $validated['severity'],
            'action_label' => trim((string) ($validated['action_label'] ?? '')) ?: null,
            'action_url' => $actionUrl ?: null,
            'starts_at' => $effectiveStartsAt,
            'ends_at' => $endsAt,
            'status' => $status,
            'published_at' => $publishedAt,
            'paused_at' => $pausedAt,
        ];
    }


    private function storeImage(Request $request): string
    {
        $file = $request->file('image');

        if (!$file || !$file->isValid()) {
            throw ValidationException::withMessages([
                'image' => 'The announcement image could not be uploaded. Please try again.',
            ]);
        }

        $path = $file->store(
            'platform-announcements/' . now('Asia/Manila')->format('Y/m'),
            'public'
        );

        if (!$path) {
            throw ValidationException::withMessages([
                'image' => 'The announcement image could not be saved. Please try again.',
            ]);
        }

        return $path;
    }

    private function deleteImage(?string $path): void
    {
        $path = trim((string) $path);

        if (
            $path !== ''
            && str_starts_with($path, 'platform-announcements/')
            && Storage::disk('public')->exists($path)
        ) {
            Storage::disk('public')->delete($path);
        }
    }

    private function manilaToUtc(string $value): Carbon
    {
        return Carbon::parse($value, 'Asia/Manila')->utc();
    }

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }
}
