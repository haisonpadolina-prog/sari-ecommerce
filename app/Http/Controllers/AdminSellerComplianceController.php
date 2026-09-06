<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchAdminSellerRealtime;
use App\Models\ComplianceMessage;
use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Models\SellerWarning;
use App\Services\SellerAccountStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminSellerComplianceController extends Controller
{
    public function __construct(
        private SellerAccountStatusService $statusService
    ) {}

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }

    /**
     * Realtime delivery is pushed to the Laravel queue so Reverb/network work
     * never holds the Admin HTTP request open.
     */
    private function queueBroadcast(
        SellerAccount $seller,
        string $type,
        string $title,
        string $message,
        ?string $productName = null,
        ?int $warningNumber = null
    ): void {
        try {
            DispatchAdminSellerRealtime::dispatch(
                sellerId: (int) $seller->id,
                kind: 'alert',
                payload: [
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'product_name' => $productName,
                    'warning_number' => $warningNumber,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning(
                'SARI seller realtime alert could not be queued.',
                [
                    'seller_id' => $seller->id,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    private function createComplianceMessage(
        SellerAccount $seller,
        string $message
    ): void {
        $record = new ComplianceMessage();

        $record->forceFill([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'message' => $message,
        ]);

        $record->save();
    }

    public function index(Request $request)
    {
        $this->guard($request);

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE ONLY POSSIBLY EXPIRED SUSPENSIONS
        |--------------------------------------------------------------------------
        |
        | The old page refreshed every SellerAccount on every visit.
        | That becomes very expensive as sellers grow. Only records whose
        | suspension timestamp has already passed can require normalization.
        */
        SellerAccount::query()
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->get()
            ->each(function (SellerAccount $seller) {
                $this->statusService->refresh($seller);
            });

        $flaggedProducts = SellerProduct::query()
            ->with(['seller', 'latestVersion'])
            ->whereNull('archived_at')
            ->where('moderation_status', 'flagged')
            ->latest('created_at')
            ->get();

        $pendingProducts = SellerProduct::query()
            ->with(['seller', 'latestVersion'])
            ->whereNull('archived_at')
            ->where('moderation_status', 'pending')
            ->latest('created_at')
            ->get();

        $recentWarnings = SellerWarning::query()
            ->with(['seller', 'product'])
            ->latest('issued_at')
            ->limit(100)
            ->get();

        $suspendedSellers = SellerAccount::query()
            ->where(function ($query) {
                $query
                    ->where('warning_count', '>=', 3)
                    ->orWhere(function ($query) {
                        $query
                            ->whereNotNull('suspended_until')
                            ->where('suspended_until', '>', now());
                    });
            })
            ->where(function ($query) {
                $query
                    ->whereNull('account_status')
                    ->orWhere('account_status', 'active');
            })
            ->latest('updated_at')
            ->get();

        $complianceMessages = ComplianceMessage::query()
            ->with('seller')
            ->latest('created_at')
            ->limit(100)
            ->get();

        $stats = [
            'total_sellers' => SellerAccount::query()->count(),
            'under_review' => $flaggedProducts->count() + $pendingProducts->count(),
            'flagged_products' => $flaggedProducts->count(),
            'active_warnings' => SellerWarning::query()->count(),
            'suspended_sellers' => $suspendedSellers->count(),
        ];

        return view('admin.seller-compliance', compact(
            'flaggedProducts',
            'pendingProducts',
            'recentWarnings',
            'suspendedSellers',
            'complianceMessages',
            'stats',
        ));
    }

    public function approve(Request $request, SellerProduct $product)
    {
        $this->guard($request);

        $product->load('seller');

        $seller = $this->statusService->refresh(
            $product->seller
        );

        if ($this->statusService->isTerminated($seller)) {
            return $this->error(
                $request,
                'product',
                'This seller account is banned or deactivated. Restore/unban the seller before approving listings.'
            );
        }

        DB::transaction(function () use ($product, $seller) {
            $freshProduct = SellerProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $freshProduct->forceFill([
                'moderation_status' => 'approved',
                'admin_review_note' => 'Approved by SARI Administrator.',
                'reviewed_at' => now(),
                'requires_re_review' => false,
            ])->save();

            $this->createComplianceMessage(
                $seller,
                'Product "' .
                $freshProduct->name .
                '" was approved by the administrator.'
            );
        });

        $this->queueBroadcast(
            $seller,
            'approved',
            'Product Approved',
            'Your product was approved by the SARI Administrator.',
            $product->name
        );

        return $this->success(
            $request,
            'Product approved successfully.'
        );
    }

    public function reject(Request $request, SellerProduct $product)
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1500'],
        ]);

        $product->load('seller');

        $seller = $product->seller;

        $reason = trim((string) ($validated['reason'] ?? ''))
            ?: 'Product rejected after administrator review.';

        DB::transaction(function () use (
            $product,
            $seller,
            $reason
        ) {
            $freshProduct = SellerProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $freshProduct->forceFill([
                'moderation_status' => 'rejected',
                'admin_review_note' => $reason,
                'reviewed_at' => now(),
                'requires_re_review' => false,
            ])->save();

            $this->createComplianceMessage(
                $seller,
                'Product "' .
                $freshProduct->name .
                '" was rejected. Reason: ' .
                $reason
            );
        });

        $this->queueBroadcast(
            $seller,
            'rejected',
            'Product Rejected',
            'Your product was rejected after administrator review. Reason: ' .
            $reason,
            $product->name
        );

        return $this->success(
            $request,
            'Product rejected.'
        );
    }

    public function warn(Request $request, SellerProduct $product)
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'admin_note' => ['nullable', 'string', 'max:1500'],
        ]);

        $result = DB::transaction(function () use (
            $product,
            $validated
        ) {
            $lockedProduct = SellerProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $seller = SellerAccount::query()
                ->whereKey($lockedProduct->seller_account_id)
                ->lockForUpdate()
                ->firstOrFail();

            $seller = $this->statusService->refresh($seller);

            if ($this->statusService->isTerminated($seller)) {
                throw ValidationException::withMessages([
                    'seller' => 'A warning cannot be issued to a banned or deactivated seller.',
                ]);
            }

            $alreadyWarned = SellerWarning::query()
                ->where(
                    'seller_product_id',
                    $lockedProduct->id
                )
                ->exists();

            if ($alreadyWarned) {
                throw ValidationException::withMessages([
                    'product' => 'A seller warning has already been issued for this product. Use a different confirmed violating product.',
                ]);
            }

            $currentWarnings = max(
                0,
                (int) ($seller->warning_count ?? 0)
            );

            if (
                $currentWarnings >= 3 ||
                $this->statusService->isSuspended($seller)
            ) {
                throw ValidationException::withMessages([
                    'seller' => 'This seller is already suspended. Use Seller Account Control for the next administrator action.',
                ]);
            }

            $warningNumber = min(
                3,
                $currentWarnings + 1
            );

            $warning = new SellerWarning();

            $warning->forceFill([
                'seller_account_id' => $seller->id,
                'seller_product_id' => $lockedProduct->id,
                'warning_number' => $warningNumber,
                'reason' => $validated['reason'],
                'admin_note' => $validated['admin_note'] ?? null,
                'issued_at' => now(),
            ]);

            $warning->save();

            $lockedProduct->forceFill([
                'moderation_status' => 'removed',
                'admin_review_note' =>
                    ($validated['admin_note'] ?? null)
                    ?: $validated['reason'],
                'reviewed_at' => now(),
                'requires_re_review' => false,
            ])->save();

            $sellerValues = [
                'warning_count' => $warningNumber,
            ];

            if ($warningNumber >= 3) {
                $sellerValues['suspended_until'] =
                    now()->addDays(30);

                $sellerValues['suspension_reason'] =
                    'Automatic 30-day suspension after warning #3. Latest violation: ' .
                    $validated['reason'];
            }

            $seller->forceFill($sellerValues)->save();

            $this->createComplianceMessage(
                $seller,
                'Compliance warning #' .
                $warningNumber .
                ' was issued for "' .
                $lockedProduct->name .
                '". Reason: ' .
                $validated['reason'] .
                (
                    $warningNumber >= 3
                        ? ' The account is now suspended for 30 days.'
                        : ''
                )
            );

            return [
                'seller_id' => (int) $seller->id,
                'warning_number' => $warningNumber,
                'product_name' => $lockedProduct->name,
            ];
        });

        $seller = SellerAccount::findOrFail(
            $result['seller_id']
        );

        $warningNumber = (int) $result['warning_number'];

        $this->queueBroadcast(
            $seller,
            $warningNumber >= 3
                ? 'suspended'
                : 'warning',
            $warningNumber >= 3
                ? 'Account Temporarily Suspended'
                : 'Compliance Warning Issued',
            $warningNumber >= 3
                ? 'You reached 3 of 3 confirmed compliance warnings. Selling privileges are suspended for 30 days. Please message the administrator from your locked dashboard if you want to request a review.'
                : 'The administrator issued warning ' .
                    $warningNumber .
                    ' of 3. Reason: ' .
                    $validated['reason'],
            $result['product_name'],
            $warningNumber
        );

        return $this->success(
            $request,
            $warningNumber >= 3
                ? 'Warning 3 / 3 issued. Seller automatically suspended for 30 days.'
                : 'Warning ' .
                    $warningNumber .
                    ' / 3 issued successfully.'
        );
    }

    public function reply(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $this->createComplianceMessage(
            $seller,
            $validated['message']
        );

        $this->queueBroadcast(
            $seller,
            'admin_message',
            'Message from SARI Administrator',
            $validated['message']
        );

        return $this->success(
            $request,
            'Reply sent to seller.'
        );
    }

    private function success(
        Request $request,
        string $message
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    private function error(
        Request $request,
        string $key,
        string $message
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    $key => [$message],
                ],
            ], 422);
        }

        return back()->withErrors([
            $key => $message,
        ]);
    }
}
