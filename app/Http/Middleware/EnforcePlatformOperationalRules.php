<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BuyerCheckoutController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\CourierPageController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RiderRegistrationController;
use App\Models\CourierAccount;
use App\Models\MarketplaceOrder;
use App\Models\PlatformSetting;
use App\Models\RiderEarning;
use App\Services\BuyerCartService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlatformOperationalRules
{
    public function __construct(
        private readonly BuyerCartService $cart
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $route = $request->route();

        if (!$route) {
            return $next($request);
        }

        $action = (string) $route->getActionName();
        [$class, $method] = array_pad(
            explode('@', $action, 2),
            2,
            ''
        );

        if (
            $class === RegistrationController::class
            && in_array($method, ['create', 'store'], true)
        ) {
            $this->assertRegistrationOpen();
        }

        if (
            $class === RiderRegistrationController::class
            && in_array($method, ['create', 'store'], true)
        ) {
            $this->assertRegistrationOpen();
        }

        if (
            $class === BuyerCheckoutController::class
            && in_array($method, ['index', 'store'], true)
        ) {
            if ($response = $this->checkoutGuard($request)) {
                return $response;
            }
        }

        if (
            $class === BuyerOrderController::class
            && $method === 'cancel'
        ) {
            if ($response = $this->buyerCancellationGuard($request)) {
                return $response;
            }
        }

        if (
            $class === CourierPageController::class
            && $method === 'requestPayout'
        ) {
            if ($response = $this->riderPayoutGuard($request)) {
                return $response;
            }
        }

        return $next($request);
    }

    private function assertRegistrationOpen(): void
    {
        if ($this->boolSetting('maintenance_mode', false)) {
            abort(
                503,
                (string) PlatformSetting::valueOf(
                    'maintenance_message',
                    'SARI registrations are temporarily unavailable.'
                )
            );
        }

        abort_if(
            !$this->boolSetting('registrations_enabled', true),
            503,
            'New SARI registrations are temporarily disabled by the administrator.'
        );
    }

    private function checkoutGuard(
        Request $request
    ): ?Response {
        if (!$request->session()->get('is_buyer')) {
            return null;
        }

        if ($this->boolSetting('maintenance_mode', false)) {
            return redirect()
                ->route('buyer.cart')
                ->withErrors([
                    'checkout' => (string) PlatformSetting::valueOf(
                        'maintenance_message',
                        'SARI checkout is temporarily unavailable.'
                    ),
                ]);
        }

        if (!$this->boolSetting('checkout_enabled', true)) {
            return redirect()
                ->route('buyer.cart')
                ->withErrors([
                    'checkout' => 'Checkout is temporarily disabled by the administrator.',
                ]);
        }

        $limit = max(
            0,
            (int) PlatformSetting::valueOf(
                'max_sellers_per_checkout',
                0
            )
        );

        if ($limit === 0) {
            return null;
        }

        $items = $this->cart->items($request);

        $buyNowId = $request->integer('buy_now')
            ?: $request->integer('buy_now_item_id');

        $selectedIds = collect(
            (array) (
                $request->input('checkout_item_ids')
                ?: $request->input('items', [])
            )
        )
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique();

        if ($buyNowId) {
            $items = $items
                ->where('id', $buyNowId)
                ->values();
        } elseif ($selectedIds->isNotEmpty()) {
            $items = $items
                ->filter(
                    fn ($item) => $selectedIds->contains(
                        (int) $item->id
                    )
                )
                ->values();
        }

        $sellerCount = $items
            ->map(
                fn ($item) => (int) (
                    $item->product?->seller_account_id ?? 0
                )
            )
            ->filter()
            ->unique()
            ->count();

        if ($sellerCount > $limit) {
            return redirect()
                ->route('buyer.cart')
                ->withErrors([
                    'checkout' => 'This checkout contains '
                        . $sellerCount
                        . ' sellers. The current platform limit is '
                        . $limit
                        . ' sellers per checkout.',
                ]);
        }

        return null;
    }

    private function buyerCancellationGuard(
        Request $request
    ): ?Response {
        $minutes = max(
            0,
            (int) PlatformSetting::valueOf(
                'buyer_cancellation_window_minutes',
                0
            )
        );

        if ($minutes === 0) {
            return null;
        }

        $parameter = $request->route('order');

        $order = $parameter instanceof MarketplaceOrder
            ? $parameter
            : MarketplaceOrder::query()->find($parameter);

        if (
            !$order
            || $order->status !== 'new'
            || !$order->created_at
        ) {
            return null;
        }

        if (now()->greaterThan($order->created_at->copy()->addMinutes(
            $minutes
        ))) {
            return back()->withErrors([
                'order' => 'The buyer cancellation window of '
                    . $minutes
                    . ' minutes has already expired for this order.',
            ]);
        }

        return null;
    }

    private function riderPayoutGuard(
        Request $request
    ): ?Response {
        $minimum = round(
            max(
                0,
                (float) PlatformSetting::valueOf(
                    'rider_payout_minimum',
                    0
                )
            ),
            2
        );

        if ($minimum <= 0) {
            return null;
        }

        $courierId = (int) $request->session()->get(
            'courier_account_id',
            0
        );

        if ($courierId <= 0) {
            $email = strtolower(
                (string) $request->session()->get(
                    'courier_email',
                    ''
                )
            );

            if ($email !== '') {
                $courierId = (int) (
                    CourierAccount::query()
                        ->whereRaw(
                            'LOWER(email) = ?',
                            [$email]
                        )
                        ->value('id') ?? 0
                );
            }
        }

        if ($courierId <= 0) {
            return null;
        }

        $available = round(
            (float) RiderEarning::query()
                ->where('courier_account_id', $courierId)
                ->where('status', 'available')
                ->sum('delivery_fee_amount'),
            2
        );

        if ($available > 0 && $available < $minimum) {
            return back()->withErrors([
                'payout' => 'A minimum available balance of ₱'
                    . number_format($minimum, 2)
                    . ' is required before requesting a rider payout. '
                    . 'Current available balance: ₱'
                    . number_format($available, 2)
                    . '.',
            ]);
        }

        return null;
    }

    private function boolSetting(
        string $key,
        bool $default
    ): bool {
        $value = PlatformSetting::valueOf(
            $key,
            $default
        );

        if (is_bool($value)) {
            return $value;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        ) ?? false;
    }
}
