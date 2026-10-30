<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform\BuyerHomepageBanner;
use App\Services\Buyer\BuyerHomepageBannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminBuyerHomepageController extends Controller
{
    public function index(Request $request, BuyerHomepageBannerService $service): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        return view('admin.buyer-homepage', [
            'banners' => $service->banners(),
            'fonts' => BuyerHomepageBannerService::FONTS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        $defaults = BuyerHomepageBannerService::defaults();
        $data = $request->validate([
            'slot' => ['required', 'string', Rule::in(array_keys($defaults))],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'image', 'max:5120', 'dimensions:min_width=400,min_height=200'],
            'kicker' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:250'],
            'button_text' => ['nullable', 'string', 'max:60'],
            'button_url' => ['required', 'string', 'max:255', function (string $attribute, mixed $value, $fail) {
                // Marketing buttons are internal only; prohibit javascript:, //host and backslashes.
                if (!is_string($value) || !preg_match('~^/(?!/)[a-zA-Z0-9/_.?=&%#-]*$~D', $value)) {
                    $fail('Use an internal SARI path starting with /, such as /buyer/products.');
                }
            }],
            'font_family' => ['required', 'string', 'max:32', 'regex:/\A[\p{L}\p{N}][\p{L}\p{N} \-]{0,31}\z/u'],
            'title_size' => ['required', 'integer', 'between:12,52'],
            'description_size' => ['required', 'integer', 'between:8,26'],
            'button_size' => ['required', 'integer', 'between:8,24'],
            'text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_bg' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_x' => ['required', 'integer', 'between:0,90'],
            'text_y' => ['required', 'integer', 'between:5,95'],
            'button_x' => ['required', 'integer', 'between:0,90'],
            'button_y' => ['required', 'integer', 'between:5,95'],
        ]);

        if (!Schema::hasTable('buyer_homepage_banners')) {
            throw ValidationException::withMessages([
                'slot' => 'Run php artisan migrate before publishing homepage banners.',
            ]);
        }

        $slot = $data['slot'];
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['kicker'] = trim($data['kicker'] ?? '');
        $data['description'] = trim($data['description'] ?? '');
        $data['button_text'] = trim($data['button_text'] ?? '');
        $data['title'] = trim($data['title']);

        // A replacement image is written before DB commit and cleaned up on failure.
        $newPath = $image ? $image->store('buyer-homepage', 'public') : null;
        $oldPath = null;

        try {
            DB::transaction(function () use ($slot, $data, $newPath, $defaults, &$oldPath) {
                $banner = BuyerHomepageBanner::query()->firstOrNew(['slot' => $slot]);
                $oldPath = $banner->exists ? $banner->image_path : null;
                $banner->fill($data);
                $banner->image_path = $newPath ?: ($oldPath ?: $defaults[$slot]['image_path']);
                $banner->save();
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && str_starts_with($oldPath, 'buyer-homepage/')) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('admin.buyer-homepage', ['slot' => $slot])
            ->with('success', 'Buyer homepage banner published successfully.');
    }
}
