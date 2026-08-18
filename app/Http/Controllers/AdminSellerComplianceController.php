<?php

namespace App\Http\Controllers;

use App\Events\SellerRealtimeAlert;
use App\Models\ComplianceMessage;
use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Models\SellerWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSellerComplianceController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        SellerAccount::query()
            ->where('account_status', 'suspended')
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->get()
            ->each(fn (SellerAccount $seller) => $seller->refreshSuspensionStatus());

        $stats = [
            'total_sellers' => SellerAccount::count(),
            'under_review' => SellerProduct::whereIn('moderation_status', ['pending', 'flagged'])->count(),
            'flagged_products' => SellerProduct::where('moderation_status', 'flagged')->count(),
            'active_warnings' => SellerWarning::count(),
            'suspended_sellers' => SellerAccount::where('account_status', 'suspended')
                ->where('suspended_until', '>', now())
                ->count(),
        ];

        $flaggedProducts = SellerProduct::with(['seller', 'latestVersion'])
            ->where('moderation_status', 'flagged')
            ->latest()
            ->get();

        $pendingProducts = SellerProduct::with(['seller', 'latestVersion'])
            ->where('moderation_status', 'pending')
            ->latest()
            ->get();

        $recentWarnings = SellerWarning::with(['seller', 'product'])
            ->latest('issued_at')
            ->take(20)
            ->get();

        $suspendedSellers = SellerAccount::where('account_status', 'suspended')
            ->where('suspended_until', '>', now())
            ->latest('suspended_at')
            ->get();

        $complianceMessages = ComplianceMessage::with('seller')
            ->latest()
            ->take(30)
            ->get();

        return view('admin.seller-compliance', compact(
            'stats',
            'flaggedProducts',
            'pendingProducts',
            'recentWarnings',
            'suspendedSellers',
            'complianceMessages'
        ));
    }

    public function approve(Request $request, SellerProduct $product)
    {
        $this->ensureAdmin($request);

        $product->update([
            'moderation_status' => 'approved',
            'admin_review_note' => 'Approved by administrator.',
            'reviewed_at' => now(),
            'requires_re_review' => false,
        ]);

        $seller = $product->seller;
        $seller->ensureRealtimeToken();

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: 'product_approved',
            title: 'Product Approved',
            message: 'Your product has been approved by the SARI administrator.',
            productName: $product->name,
        ));

        return back()->with('success', 'Product approved. Seller was updated in real time.');
    }

    public function reject(Request $request, SellerProduct $product)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $product->update([
            'moderation_status' => 'rejected',
            'admin_review_note' => $validated['reason'],
            'reviewed_at' => now(),
            'requires_re_review' => false,
        ]);

        $seller = $product->seller;
        $seller->ensureRealtimeToken();

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: 'product_rejected',
            title: 'Product Review Update',
            message: 'Your product was rejected. Admin reason: ' . $validated['reason'],
            productName: $product->name,
        ));

        return back()->with('success', 'Product rejected without issuing a warning.');
    }

    public function warn(Request $request, SellerProduct $product)
    {
        $this->ensureAdmin($request);

        if ($product->warnings()->exists()) {
            return back()->withErrors([
                'warning' => 'A compliance warning has already been issued for this product. Use another confirmed violation for the next warning.',
            ]);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $result = DB::transaction(function () use ($product, $validated) {
            /** @var SellerAccount $seller */
            $seller = SellerAccount::query()
                ->lockForUpdate()
                ->findOrFail($product->seller_account_id);

            $seller->refreshSuspensionStatus();
            $nextWarning = min($seller->warning_count + 1, 3);

            SellerWarning::create([
                'seller_account_id' => $seller->id,
                'seller_product_id' => $product->id,
                'warning_number' => $nextWarning,
                'reason' => $validated['reason'],
                'admin_note' => $validated['admin_note'] ?? null,
                'issued_at' => now(),
            ]);

            $product->update([
                'moderation_status' => 'removed',
                'admin_review_note' => $validated['admin_note'] ?? $validated['reason'],
                'reviewed_at' => now(),
                'requires_re_review' => false,
            ]);

            $seller->warning_count = $nextWarning;

            if ($nextWarning >= 3) {
                $seller->account_status = 'suspended';
                $seller->suspended_at = now();
                $seller->suspended_until = now()->addDays(30);
                $seller->suspension_reason = 'Automatic 30-day suspension after the third confirmed marketplace compliance warning.';
            } else {
                $seller->account_status = 'warning';
            }

            $seller->ensureRealtimeToken();
            $seller->save();

            ComplianceMessage::create([
                'seller_account_id' => $seller->id,
                'sender_role' => 'admin',
                'message' => $nextWarning >= 3
                    ? 'Compliance warning #' . $nextWarning . ' was issued for "' . $product->name . '". Your selling privileges are suspended for 30 days. You may message the administrator to appeal.'
                    : 'Compliance warning #' . $nextWarning . ' was issued for "' . $product->name . '". Reason: ' . $validated['reason'],
            ]);

            return [
                'seller' => $seller->fresh(),
                'warning_number' => $nextWarning,
            ];
        });

        /** @var SellerAccount $seller */
        $seller = $result['seller'];
        $warningNumber = $result['warning_number'];

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: $warningNumber >= 3 ? 'suspended' : 'warning',
            title: $warningNumber >= 3 ? 'Account Temporarily Suspended' : 'Compliance Warning Issued',
            message: $warningNumber >= 3
                ? 'You reached 3 of 3 confirmed compliance warnings. Selling privileges are suspended for 30 days.'
                : 'The administrator issued warning ' . $warningNumber . ' of 3. Reason: ' . $validated['reason'],
            productName: $product->name,
            warningNumber: $warningNumber,
            suspendedUntil: $seller->suspended_until?->toIso8601String(),
        ));

        return back()->with(
            'success',
            $warningNumber >= 3
                ? 'Third warning issued. Seller suspended for 30 days and notified in real time.'
                : 'Warning issued and seller notified in real time.'
        );
    }

    public function suspend30(Request $request, SellerAccount $seller)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $seller->update([
            'account_status' => 'suspended',
            'suspended_at' => now(),
            'suspended_until' => now()->addDays(30),
            'suspension_reason' => $validated['reason'],
        ]);

        $seller->ensureRealtimeToken();

        ComplianceMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'message' => 'Your seller account was manually suspended for 30 days. Reason: ' . $validated['reason'],
        ]);

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: 'suspended',
            title: 'Account Temporarily Suspended',
            message: 'The administrator suspended your selling privileges for 30 days. Reason: ' . $validated['reason'],
            warningNumber: $seller->warning_count,
            suspendedUntil: $seller->suspended_until?->toIso8601String(),
        ));

        return back()->with('success', 'Seller suspended for 30 days and notified in real time.');
    }

    public function unsuspend(Request $request, SellerAccount $seller)
    {
        $this->ensureAdmin($request);

        $seller->update([
            'account_status' => $seller->warning_count > 0 ? 'warning' : 'active',
            'suspended_at' => null,
            'suspended_until' => null,
            'suspension_reason' => null,
        ]);

        $seller->ensureRealtimeToken();

        ComplianceMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'message' => 'Your seller suspension has been lifted by the administrator.',
        ]);

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: 'suspension_lifted',
            title: 'Suspension Lifted',
            message: 'Your seller suspension has been lifted. Selling actions are available again.',
            warningNumber: $seller->warning_count,
        ));

        return back()->with('success', 'Seller suspension lifted.');
    }

    public function reply(Request $request, SellerAccount $seller)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ComplianceMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'message' => $validated['message'],
        ]);

        $seller->ensureRealtimeToken();

        event(new SellerRealtimeAlert(
            seller: $seller,
            type: 'admin_message',
            title: 'New Message from SARI Admin',
            message: $validated['message'],
        ));

        return back()->with('success', 'Reply sent to seller.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->session()->get('is_admin'), 403);
    }
}
