<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\RiderEarning;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\PlatformSetting;
use App\Services\Shared\AccountProfileService;
use App\Services\Finance\PayoutService;
use App\Services\Finance\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourierPageController extends Controller
{
    private const ACTIVE_DIRECT = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    private const ACTIVE_LAST_MILE = [
        'assigned',
        'out_for_delivery',
    ];

    private const TZ = 'Asia/Manila';

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_courier'),
            403,
            'Rider session required.'
        );
    }

    private function courierAccount(Request $request): CourierAccount
    {
        $this->guard($request);

        $id = (int) $request
            ->session()
            ->get(
                'courier_account_id',
                0
            );

        if ($id > 0) {
            $account = CourierAccount::query()
                ->whereKey($id)
                ->where(
                    'account_status',
                    'active'
                )
                ->first();

            abort_unless(
                $account,
                403,
                'Active Rider account required.'
            );

            return $account;
        }

        $email = strtolower(
            trim(
                (string) $request
                    ->session()
                    ->get(
                        'courier_email',
                        ''
                    )
            )
        );

        abort_if(
            $email === '',
            403,
            'Rider identity is missing from the session.'
        );

        $account = CourierAccount::query()
            ->whereRaw(
                'LOWER(email) = ?',
                [
                    $email,
                ]
            )
            ->where(
                'account_status',
                'active'
            )
            ->first();

        if ($account) {
            $request
                ->session()
                ->put(
                    'courier_account_id',
                    $account->id
                );

            return $account;
        }

        abort_unless(
            $email === 'courier@gmail.com',
            403,
            'Rider account is no longer available.'
        );

        $account = CourierAccount::create([
            'registration_application_id' =>
                null,

            'last_name' =>
                'Rider',

            'first_name' =>
                'SARI',

            'middle_initial' =>
                null,

            'sex' =>
                'Male',

            'email' =>
                $email,

            'contact_no' =>
                '09123456789',

            'birthday' =>
                '2000-01-01',

            'age' =>
                26,

            'province_code' =>
                'TEST',

            'province_name' =>
                'Metro Manila',

            'municipality_code' =>
                'TEST',

            'municipality_name' =>
                'Manila',

            'barangay_code' =>
                'TEST',

            'barangay_name' =>
                'Test Barangay',

            'street_address' =>
                'SARI Rider Test Address',

            'password' =>
                Hash::make(
                    'courier123'
                ),

            'vehicle_type' =>
                'Motorcycle',

            'plate_number' =>
                'TEST-001',

            'account_status' =>
                'active',

            'approved_at' =>
                now(),

            'availability_status' =>
                'online',

            'rating' =>
                5.00,
        ]);

        $request
            ->session()
            ->put(
                'courier_account_id',
                $account->id
            );

        return $account;
    }

    private function ordersFor(
        CourierAccount $account
    ) {
        return MarketplaceOrder::query()
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [
                    strtolower(
                        trim(
                            (string) $account
                                ->email
                        )
                    ),
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Earnings
    |--------------------------------------------------------------------------
    |
    | Completed earnings come only from rider_earnings.
    |
    | Pending:
    | - direct Seller-to-Buyer jobs
    | - Logistics pickup-leg share
    | - active Last-Mile share
    |
    | Completed earnings are leg-aware: pickup, last-mile, or direct delivery.
    |
    */

    public function earnings(
        Request $request
    ): View {
        $account = $this->courierAccount($request);

        $ledger = RiderEarning::query()
            ->with('order')
            ->where('courier_account_id', $account->id)
            ->latest('earned_at')
            ->get();

        $wallet = app(WalletService::class)->wallet('courier', $account->id);

        $directPending = (float) $this
            ->ordersFor($account)
            ->whereDoesntHave('logisticsParcel')
            ->whereIn('status', self::ACTIVE_DIRECT)
            ->get()
            ->sum(fn (MarketplaceOrder $order): float => $order->courierDeliveryFee());

        $pickupPercent = max(0, min(
            100,
            (float) PlatformSetting::valueOf('shipping_pickup_rider_percent', 25)
        ));
        $lastMilePercent = max(0, min(
            100,
            (float) PlatformSetting::valueOf('shipping_last_mile_rider_percent', 45)
        ));

        $pickupPendingGross = (float) MarketplaceOrder::query()
            ->whereNotNull('logistics_account_id')
            ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $account->email)])
            ->whereHas('logisticsParcel')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get()
            ->sum(fn (MarketplaceOrder $order): float => $order->courierDeliveryFee());

        $pickupPending = round($pickupPendingGross * ($pickupPercent / 100), 2);

        $lastMilePendingGross = (float) DB::table('last_mile_assignments as assignment')
            ->join(
                'last_mile_assignment_orders as link',
                'link.last_mile_assignment_id',
                '=',
                'assignment.id'
            )
            ->join(
                'marketplace_orders as orders',
                'orders.id',
                '=',
                'link.marketplace_order_id'
            )
            ->where('assignment.courier_account_id', $account->id)
            ->whereIn('assignment.status', self::ACTIVE_LAST_MILE)
            ->whereIn('orders.status', ['in_transit', 'arrived_buyer'])
            ->sum(DB::raw('CASE WHEN orders.original_delivery_fee > 0 THEN orders.original_delivery_fee ELSE orders.delivery_fee END'));

        $lastMilePending = round($lastMilePendingGross * ($lastMilePercent / 100), 2);

        $now = now(self::TZ);
        $today = $now->copy()->startOfDay();
        $week = $now->copy()->startOfWeek();
        $month = $now->copy()->startOfMonth();

        $earnings = [
            'today' => $this->sumSince($ledger, $today),
            'week' => $this->sumSince($ledger, $week),
            'month' => $this->sumSince($ledger, $month),
            'pending' => $directPending + $pickupPending + $lastMilePending,
            'awaiting_remittance' => (float) $ledger
                ->where('status', 'pending_remittance')
                ->sum('delivery_fee_amount'),
            'available' => (float) $wallet->available_balance,
            'reserved' => (float) $wallet->reserved_balance,
            'paid' => (float) $wallet->lifetime_payouts,
        ];

        $daily = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($ledger, $now): array {
                $date = $now->copy()->subDays($daysAgo);

                return [
                    'day' => $date->format('D'),
                    'amount' => (float) $ledger
                        ->filter(function (RiderEarning $earning) use ($date): bool {
                            return $earning->earned_at
                                ?->copy()
                                ->setTimezone(self::TZ)
                                ->isSameDay($date) ?? false;
                        })
                        ->sum('delivery_fee_amount'),
                ];
            })
            ->all();

        $transactions = $ledger
            ->take(20)
            ->map(function (RiderEarning $earning): array {
                $type = match ($earning->earning_type) {
                    'pickup_leg' => 'Pickup: Seller → Hub',
                    'last_mile_leg' => 'Last Mile: Hub → Buyer',
                    default => 'Direct Delivery',
                };

                return [
                    'order' => $earning->order?->order_number ?: '—',
                    'date' => $earning->earned_at
                        ?->copy()
                        ->setTimezone(self::TZ)
                        ->format('M d, Y · h:i A') ?? '—',
                    'type' => $type,
                    'amount' => (float) $earning->delivery_fee_amount,
                    'status' => match ($earning->status) {
                        'pending_remittance' => 'Awaiting COD remittance',
                        'available' => 'Available in wallet',
                        'reserved' => 'Reserved for payout',
                        'paid' => 'Paid',
                        default => ucfirst((string) $earning->status),
                    },
                ];
            })
            ->values()
            ->all();

        $previousStart = $now->copy()->subWeek()->startOfWeek();
        $previousEnd = $now->copy()->subWeek()->endOfWeek();

        $previousWeek = (float) $ledger
            ->filter(function (RiderEarning $earning) use ($previousStart, $previousEnd): bool {
                $earnedAt = $earning->earned_at?->copy()->setTimezone(self::TZ);

                return $earnedAt?->between($previousStart, $previousEnd) ?? false;
            })
            ->sum('delivery_fee_amount');

        $weeklyChange = $previousWeek > 0
            ? (($earnings['week'] - $previousWeek) / $previousWeek) * 100
            : null;

        $payoutRequests = FinancePayoutRequest::query()
            ->where('owner_type', 'courier')
            ->where('owner_id', $account->id)
            ->latest('requested_at')
            ->limit(10)
            ->get();

        $walletEntries = $wallet->entries()
            ->latest('occurred_at')
            ->limit(20)
            ->get();

        return view('courier.earnings', compact(
            'earnings',
            'daily',
            'transactions',
            'weeklyChange',
            'payoutRequests',
            'wallet',
            'walletEntries'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery History
    |--------------------------------------------------------------------------
    |
    | Delivery history shows final Buyer deliveries only. Pickup-leg earnings
    | remain visible on Earnings but are excluded from completed-delivery stats.
    |
    */

    public function history(
        Request $request
    ): View {
        $account =
            $this->courierAccount(
                $request
            );

        $ledger =
            RiderEarning::query()
                ->with('order')
                ->where(
                    'courier_account_id',
                    $account->id
                )
                ->whereIn('earning_type', ['direct_delivery', 'last_mile_leg'])
                ->latest(
                    'earned_at'
                )
                ->limit(100)
                ->get();

        $lastMileRoutes =
            $this->lastMileRoutesFor(
                $account,
                $ledger
                    ->pluck(
                        'marketplace_order_id'
                    )
                    ->filter()
                    ->all()
            );

        $history =
            $ledger
                ->map(
                    function (
                        RiderEarning $earning
                    ) use (
                        $lastMileRoutes
                    ): array {
                        $order =
                            $earning->order;

                        $lastMile =
                            $lastMileRoutes
                                ->get(
                                    (int) $earning
                                        ->marketplace_order_id
                                );

                        $earnedAt =
                            $earning
                                ->earned_at
                                ?->copy()
                                ->setTimezone(
                                    self::TZ
                                );

                        return [
                            'order' =>
                                $order
                                    ?->order_number
                                ?: '—',

                            'customer' =>
                                $order
                                    ?->buyer_name
                                ?: 'Buyer',

                            'route' =>
                                $lastMile
                                    ? (
                                        $lastMile
                                            ->facility_name
                                        . ' → '
                                        . (
                                            $lastMile
                                                ->delivery_area
                                            ?: (
                                                $order
                                                    ?->buyer_name
                                                ?: 'Buyer'
                                            )
                                        )
                                    )
                                    : (
                                        (
                                            $order
                                                ?->pickup_name
                                            ?: 'Seller'
                                        )
                                        . ' → '
                                        . (
                                            $order
                                                ?->buyer_name
                                            ?: 'Buyer'
                                        )
                                    ),

                            'date' =>
                                $earnedAt
                                    ?->format(
                                        'M d, Y'
                                    )
                                ?? '—',

                            'time' =>
                                $earnedAt
                                    ?->format(
                                        'h:i A'
                                    )
                                ?? '—',

                            'fee' =>
                                (float) $earning
                                    ->delivery_fee_amount,

                            'status' =>
                                'Completed',

                            'is_today' =>
                                $earnedAt
                                    ?->isSameDay(
                                        now(
                                            self::TZ
                                        )
                                    )
                                ?? false,
                        ];
                    }
                )
                ->all();

        $completed =
            collect(
                $history
            );

        $historyStats = [
            'today' =>
                $completed
                    ->where(
                        'is_today',
                        true
                    )
                    ->count(),

            'total' =>
                $completed
                    ->count(),

            'success_rate' =>
                $completed
                    ->isNotEmpty()
                    ? 100.0
                    : 0.0,

            'average_fee' =>
                $completed
                    ->isNotEmpty()
                    ? (float) $completed
                        ->avg(
                            'fee'
                        )
                    : 0.0,
        ];

        return view(
            'courier.history',
            compact(
                'history',
                'historyStats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payout Request
    |--------------------------------------------------------------------------
    */

    public function requestPayout(
        Request $request
    ): RedirectResponse {
        $account = $this->courierAccount($request);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'payout_method' => ['required', 'in:gcash,maya,bank,manual_cash'],
            'account_name' => ['required', 'string', 'min:2', 'max:120'],
            'account_reference' => ['required', 'string', 'min:4', 'max:120'],
        ]);

        app(PayoutService::class)->request(
            'courier',
            $account->id,
            (float) $validated['amount'],
            $validated['payout_method'],
            $validated['account_name'],
            $validated['account_reference']
        );

        return back()->with(
            'success',
            'Withdrawal request submitted. The amount is reserved until Admin confirms the external payout.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Earnings CSV
    |--------------------------------------------------------------------------
    */

    public function earningsStatement(
        Request $request
    ): StreamedResponse {
        $account =
            $this->courierAccount(
                $request
            );

        $earnings =
            RiderEarning::query()
                ->with('order')
                ->where(
                    'courier_account_id',
                    $account->id
                )
                ->latest(
                    'earned_at'
                )
                ->get();

        return response()->streamDownload(
            function () use (
                $earnings
            ): void {
                $out =
                    fopen(
                        'php://output',
                        'w'
                    );

                fwrite(
                    $out,
                    "\xEF\xBB\xBF"
                );

                fputcsv(
                    $out,
                    [
                        'Order',
                        'Earned At',
                        'Buyer',
                        'Delivery Fee',
                        'Ledger Status',
                    ]
                );

                foreach (
                    $earnings
                    as $earning
                ) {
                    fputcsv(
                        $out,
                        [
                            $earning
                                ->order
                                ?->order_number,

                            $earning
                                ->earned_at
                                ?->copy()
                                ->setTimezone(
                                    self::TZ
                                )
                                ->toDateTimeString(),

                            $earning
                                ->order
                                ?->buyer_name,

                            $earning
                                ->delivery_fee_amount,

                            $earning
                                ->status,
                        ]
                    );
                }

                fclose(
                    $out
                );
            },
            'rider-earnings-statement.csv',
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery History CSV
    |--------------------------------------------------------------------------
    */

    public function historyExport(
        Request $request
    ): StreamedResponse {
        $account =
            $this->courierAccount(
                $request
            );

        $earnings =
            RiderEarning::query()
                ->with('order')
                ->where(
                    'courier_account_id',
                    $account->id
                )
                ->whereIn('earning_type', ['direct_delivery', 'last_mile_leg'])
                ->latest(
                    'earned_at'
                )
                ->get();

        return response()->streamDownload(
            function () use (
                $earnings
            ): void {
                $out =
                    fopen(
                        'php://output',
                        'w'
                    );

                fwrite(
                    $out,
                    "\xEF\xBB\xBF"
                );

                fputcsv(
                    $out,
                    [
                        'Order',
                        'Buyer',
                        'Delivered At',
                        'Delivery Fee',
                        'Earning Status',
                    ]
                );

                foreach (
                    $earnings
                    as $earning
                ) {
                    fputcsv(
                        $out,
                        [
                            $earning
                                ->order
                                ?->order_number,

                            $earning
                                ->order
                                ?->buyer_name,

                            $earning
                                ->earned_at
                                ?->copy()
                                ->setTimezone(
                                    self::TZ
                                )
                                ->toDateTimeString(),

                            $earning
                                ->delivery_fee_amount,

                            $earning
                                ->status,
                        ]
                    );
                }

                fclose(
                    $out
                );
            },
            'rider-delivery-history.csv',
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rider ↔ Logistics Messaging
    |--------------------------------------------------------------------------
    |
    | Conversation is scoped to both Rider and Logistics provider.
    |
    */

    public function messages(
        Request $request
    ): View {
        $account =
            $this->courierAccount(
                $request
            );

        $logisticsAccountId =
            (int) (
                $account
                    ->logistics_account_id
                ?? 0
            );

        if (
            $logisticsAccountId
            <= 0
        ) {
            $messages =
                collect();

            return view(
                'courier.messages',
                compact(
                    'account',
                    'messages'
                )
            );
        }

        LogisticsMessage::query()
            ->where(
                'courier_account_id',
                $account->id
            )
            ->where(
                'logistics_account_id',
                $logisticsAccountId
            )
            ->where(
                'sender_role',
                'logistics'
            )
            ->whereNull(
                'read_at'
            )
            ->update([
                'read_at' =>
                    now(),
            ]);

        $messages =
            LogisticsMessage::query()
                ->where(
                    'courier_account_id',
                    $account->id
                )
                ->where(
                    'logistics_account_id',
                    $logisticsAccountId
                )
                ->oldest(
                    'id'
                )
                ->get();

        return view(
            'courier.messages',
            compact(
                'account',
                'messages'
            )
        );
    }

    public function sendMessage(
        Request $request
    ): RedirectResponse {
        $account =
            $this->courierAccount(
                $request
            );

        $logisticsAccountId =
            (int) (
                $account
                    ->logistics_account_id
                ?? 0
            );

        if (
            $logisticsAccountId
            <= 0
        ) {
            return back()->withErrors([
                'message' =>
                    'This Rider is not currently connected to a Logistics provider.',
            ]);
        }

        $validated =
            $request->validate([
                'body' => [
                    'required',
                    'string',
                    'max:3000',
                ],
            ]);

        LogisticsMessage::create([
            'courier_account_id' =>
                $account->id,

            'logistics_account_id' =>
                $logisticsAccountId,

            'sender_role' =>
                'rider',

            'body' =>
                $validated['body'],
        ]);

        return back()->with(
            'success',
            'Message sent to Logistics.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rider Profile
    |--------------------------------------------------------------------------
    */

    public function profile(
        Request $request
    ): View {
        $account =
            $this->courierAccount(
                $request
            );

        /*
        | RiderEarning is the source of truth for successful delivery credit.
        */

        $completed =
            RiderEarning::query()
                ->where(
                    'courier_account_id',
                    $account->id
                )
                ->count();

        $profile = [
            'full_name' =>
                trim(
                    $account->first_name
                    . ' '
                    . $account->last_name
                ),

            'email' =>
                $account->email,

            'phone' =>
                $account->contact_no,

            'address' =>
                trim(
                    $account->street_address
                    . ', '
                    . $account->barangay_name
                    . ', '
                    . $account->municipality_name
                    . ', '
                    . $account->province_name,
                    ', '
                ),

            'vehicle_type' =>
                $account->vehicle_type,

            'vehicle_model' =>
                $account->vehicle_model
                ?: '—',

            'plate_number' =>
                $account->plate_number,

            'license_number' =>
                $account->license_number
                ?: '—',

            'rating' =>
                number_format(
                    (float) (
                        $account->rating
                        ?? 5
                    ),
                    2
                ),

            'completed_deliveries' =>
                $completed,

            'joined' =>
                $account
                    ->created_at
                    ?->format(
                        'F Y'
                    )
                ?? '—',

            /*
            | NULL is not treated as online.
            */
            'availability_status' =>
                $account
                    ->availability_status
                === 'online'
                    ? 'online'
                    : 'offline',
        ];

        return view(
            'courier.profile',
            compact(
                'account',
                'profile'
            )
        );
    }

    public function updateProfile(
        Request $request,
        AccountProfileService $profiles
    ): RedirectResponse {
        $account =
            $this->courierAccount(
                $request
            );

        $validated =
            $request->validate([
                'profile_image' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

                'first_name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'last_name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'contact_no' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',

                    Rule::unique(
                        'courier_accounts',
                        'email'
                    )->ignore(
                        $account->id
                    ),
                ],

                'street_address' => [
                    'required',
                    'string',
                    'max:1000',
                ],

                'vehicle_type' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'vehicle_model' => [
                    'nullable',
                    'string',
                    'max:120',
                ],

                'plate_number' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'license_number' => [
                    'nullable',
                    'string',
                    'max:120',
                ],

                'current_password' => [
                    'nullable',
                    'string',
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ]);

        if (
            ! empty(
                $validated['password']
            )
        ) {
            if (
                ! Hash::check(
                    (string) (
                        $validated[
                            'current_password'
                        ]
                        ?? ''
                    ),
                    $account->password
                )
            ) {
                return back()->withErrors([
                    'current_password' =>
                        'Current password is incorrect.',
                ]);
            }

            $account->password =
                Hash::make(
                    $validated['password']
                );
        }

        $profiles->save(
            $account,
            [
                'first_name' =>
                    $validated[
                        'first_name'
                    ],

                'last_name' =>
                    $validated[
                        'last_name'
                    ],

                'contact_no' =>
                    $validated[
                        'contact_no'
                    ],

                'email' =>
                    strtolower(
                        $validated[
                            'email'
                        ]
                    ),

                'street_address' =>
                    $validated[
                        'street_address'
                    ],

                'vehicle_type' =>
                    $validated[
                        'vehicle_type'
                    ],

                'vehicle_model' =>
                    $validated[
                        'vehicle_model'
                    ]
                    ?? null,

                'plate_number' =>
                    $validated[
                        'plate_number'
                    ],

                'license_number' =>
                    $validated[
                        'license_number'
                    ]
                    ?? null,
            ],
            $request
        );

        $request
            ->session()
            ->put(
                'courier_email',
                $account->email
            );

        $request
            ->session()
            ->put(
                'courier_name',
                trim(
                    $account->first_name
                    . ' '
                    . $account->last_name
                )
            );

        return back()->with(
            'success',
            'Rider profile updated.'
        );
    }

    public function availability(
        Request $request
    ): RedirectResponse {
        $account =
            $this->courierAccount(
                $request
            );

        $validated =
            $request->validate([
                'availability_status' => [
                    'required',

                    Rule::in([
                        'online',
                        'offline',
                    ]),
                ],
            ]);

        $account->update([
            'availability_status' =>
                $validated[
                    'availability_status'
                ],
        ]);

        return back()->with(
            'success',
            'Availability updated.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function sumSince(
        $ledger,
        $boundary
    ): float {
        return (float) $ledger
            ->filter(
                function (
                    RiderEarning $earning
                ) use (
                    $boundary
                ): bool {
                    return $earning
                        ->earned_at
                        ?->copy()
                        ->setTimezone(
                            self::TZ
                        )
                        ->greaterThanOrEqualTo(
                            $boundary
                        )
                        ?? false;
                }
            )
            ->sum(
                'delivery_fee_amount'
            );
    }

    private function lastMileRoutesFor(
        CourierAccount $account,
        array $orderIds
    ) {
        if (
            empty(
                $orderIds
            )
        ) {
            return collect();
        }

        return DB::table(
            'last_mile_assignment_orders as link'
        )
            ->join(
                'last_mile_assignments as assignment',
                'assignment.id',
                '=',
                'link.last_mile_assignment_id'
            )
            ->join(
                'logistics_facilities as facility',
                'facility.id',
                '=',
                'assignment.destination_facility_id'
            )
            ->where(
                'assignment.courier_account_id',
                $account->id
            )
            ->whereIn(
                'link.marketplace_order_id',
                $orderIds
            )
            ->select([
                'link.marketplace_order_id',
                'assignment.delivery_area',
                'facility.name as facility_name',
            ])
            ->get()
            ->keyBy(
                'marketplace_order_id'
            );
    }
}