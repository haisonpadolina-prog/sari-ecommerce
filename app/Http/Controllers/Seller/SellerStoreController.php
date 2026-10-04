<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SellerStoreController extends Controller
{
    public function index(Request $request): View
    {
        return view('seller.store', [
            'seller' => $this->seller($request),
        ]);
    }

    public function update(
        Request $request
    ): RedirectResponse|JsonResponse {
        $seller = $this->seller($request);

        $requiredColumns = [
            'pickup_address',
            'store_logo_path',
            'store_banner_path',
        ];

        $missingColumns = collect($requiredColumns)
            ->reject(
                fn (string $column): bool =>
                    Schema::hasColumn(
                        'seller_accounts',
                        $column
                    )
            )
            ->values();

        if ($missingColumns->isNotEmpty()) {
            Log::error(
                'Store Management database columns are missing.',
                [
                    'seller_account_id' =>
                        $seller->getKey(),
                    'missing_columns' =>
                        $missingColumns->all(),
                ]
            );

            return $this->errorResponse(
                $request,
                'Store Management is not fully installed yet. '
                .'Please run php artisan migrate, then try saving again.',
                422
            );
        }

        $validated = $request->validate([
            'store_name' => [
                'required',
                'string',
                'max:150',
            ],
            'store_description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'store_phone' => [
                'nullable',
                'string',
                'max:40',
            ],
            'store_public_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'store_status' => [
                'required',
                Rule::in(['open', 'paused']),
            ],
            'pickup_address' => [
                'required',
                'string',
                'max:1000',
            ],
            'pickup_instructions' => [
                'nullable',
                'string',
                'max:1200',
            ],
            'store_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'store_banner' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
            ],
            'remove_store_logo' => [
                'nullable',
                'boolean',
            ],
            'remove_store_banner' => [
                'nullable',
                'boolean',
            ],
        ], [
            'store_logo.image' =>
                'Store logo must be a valid image.',
            'store_logo.mimes' =>
                'Store logo must be JPG, JPEG, PNG, or WEBP.',
            'store_logo.max' =>
                'Store logo must not be larger than 5 MB.',
            'store_banner.image' =>
                'Store banner must be a valid image.',
            'store_banner.mimes' =>
                'Store banner must be JPG, JPEG, PNG, or WEBP.',
            'store_banner.max' =>
                'Store banner must not be larger than 8 MB.',
        ]);

        $attributes = collect($validated)
            ->except([
                'store_logo',
                'store_banner',
                'remove_store_logo',
                'remove_store_banner',
            ])
            ->all();

        $newLogoPath = null;
        $newBannerPath = null;

        $oldLogoPath = $seller->getAttribute(
            'store_logo_path'
        );

        $oldBannerPath = $seller->getAttribute(
            'store_banner_path'
        );

        try {
            if ($request->hasFile('store_logo')) {
                $newLogoPath = $request
                    ->file('store_logo')
                    ->store(
                        'seller-store-media/'
                        .$seller->getKey()
                        .'/logo',
                        'public'
                    );

                if (!$newLogoPath) {
                    return $this->errorResponse(
                        $request,
                        'The Store logo could not be saved. Please try again.',
                        422
                    );
                }

                $attributes['store_logo_path'] =
                    $newLogoPath;
            } elseif (
                $request->boolean(
                    'remove_store_logo'
                )
            ) {
                $attributes['store_logo_path'] =
                    null;
            }

            if ($request->hasFile('store_banner')) {
                $newBannerPath = $request
                    ->file('store_banner')
                    ->store(
                        'seller-store-media/'
                        .$seller->getKey()
                        .'/banner',
                        'public'
                    );

                if (!$newBannerPath) {
                    if ($newLogoPath) {
                        Storage::disk('public')
                            ->delete($newLogoPath);
                    }

                    return $this->errorResponse(
                        $request,
                        'The Store banner could not be saved. Please try again.',
                        422
                    );
                }

                $attributes['store_banner_path'] =
                    $newBannerPath;
            } elseif (
                $request->boolean(
                    'remove_store_banner'
                )
            ) {
                $attributes['store_banner_path'] =
                    null;
            }

            $seller->forceFill($attributes)->save();
        } catch (Throwable $exception) {
            if ($newLogoPath) {
                try {
                    Storage::disk('public')
                        ->delete($newLogoPath);
                } catch (Throwable) {
                    // Preserve the original save error.
                }
            }

            if ($newBannerPath) {
                try {
                    Storage::disk('public')
                        ->delete($newBannerPath);
                } catch (Throwable) {
                    // Preserve the original save error.
                }
            }

            Log::error(
                'Store Management save failed.',
                [
                    'seller_account_id' =>
                        $seller->getKey(),
                    'exception' =>
                        $exception->getMessage(),
                ]
            );

            report($exception);

            return $this->errorResponse(
                $request,
                'Store changes could not be saved. '
                .'Your existing Store data was kept. '
                .'Please try again.',
                500
            );
        }

        /*
        | Delete old media only after the database save succeeds.
        */
        if (
            $oldLogoPath
            && (
                $newLogoPath
                || $request->boolean(
                    'remove_store_logo'
                )
            )
            && $oldLogoPath
                !== $seller->getAttribute(
                    'store_logo_path'
                )
        ) {
            try {
                Storage::disk('public')
                    ->delete($oldLogoPath);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if (
            $oldBannerPath
            && (
                $newBannerPath
                || $request->boolean(
                    'remove_store_banner'
                )
            )
            && $oldBannerPath
                !== $seller->getAttribute(
                    'store_banner_path'
                )
        ) {
            try {
                Storage::disk('public')
                    ->delete($oldBannerPath);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $seller->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'message' =>
                    'Store information and media updated successfully.',
                'store' => [
                    'store_name' =>
                        (string) $seller->store_name,
                    'store_description' =>
                        (string) ($seller->store_description ?? ''),
                    'store_phone' =>
                        (string) ($seller->store_phone ?? ''),
                    'store_public_email' =>
                        (string) ($seller->store_public_email ?? ''),
                    'store_status' =>
                        (string) ($seller->store_status ?: 'open'),
                    'pickup_address' =>
                        (string) ($seller->pickup_address ?? ''),
                    'pickup_instructions' =>
                        (string) ($seller->pickup_instructions ?? ''),
                    'store_logo_url' =>
                        $seller->store_logo_path
                            ? Storage::disk('public')
                                ->url($seller->store_logo_path)
                            : null,
                    'store_banner_url' =>
                        $seller->store_banner_path
                            ? Storage::disk('public')
                                ->url($seller->store_banner_path)
                            : null,
                ],
            ]);
        }

        return back()->with(
            'success',
            'Store information and media updated successfully.'
        );
    }

    private function errorResponse(
        Request $request,
        string $message,
        int $status
    ): RedirectResponse|JsonResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    'store' => [$message],
                ],
            ], $status);
        }

        return back()
            ->withErrors([
                'store' => $message,
            ])
            ->withInput(
                $request->except([
                    'store_logo',
                    'store_banner',
                ])
            );
    }

    private function seller(
        Request $request
    ): SellerAccount {
        $seller = $request->attributes->get(
            'sellerAccount'
        );

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get(
                'seller_account_id'
            )
        );
    }
}
