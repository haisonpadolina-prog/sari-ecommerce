<?php

use App\Events\SellerOrderUpdated;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Finance\AdminCashTurnover;
use App\Models\Finance\AdminCashTurnoverCount;
use App\Models\Finance\LogisticsEarning;
use App\Models\Finance\RiderEarning;
use App\Services\Finance\AdminCashTurnoverService;
use App\Services\Finance\BranchTreasuryService;
use App\Services\Finance\CodRemittanceService;
use App\Services\Finance\FinanceReleaseService;
use App\Services\Finance\FinancialFlowService;
use App\Services\Finance\PayoutService;
use App\Services\Finance\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Event::fake([SellerOrderUpdated::class]);
});

function financeLogistics(): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'last_name' => 'Logistics',
        'first_name' => 'Finance',
        'sex' => 'N/A',
        'email' => fake()->unique()->safeEmail(),
        'contact_no' => '09170000001',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'CAV',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Test Barangay',
        'street_address' => 'Test Logistics Address',
        'business_name' => 'Finance Logistics',
        'password' => Hash::make('finance-logistics-password'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function financeAdmin(): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Finance Admin',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('finance-admin-password'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

function attachLastMile(int $logisticsId, int $riderId, int $orderId, bool $isMainBranch = true): LogisticsFacility
{
    $facility = LogisticsFacility::query()->create([
        'logistics_account_id' => $logisticsId,
        'code' => 'FIN-'.fake()->unique()->numerify('#####'),
        'name' => 'Finance Destination Hub',
        'address' => 'Test Hub Address',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Test Barangay',
        'latitude' => 14.4297,
        'longitude' => 120.9367,
        'is_active' => true,
        'is_main_branch' => $isMainBranch,
    ]);

    $assignmentId = DB::table('last_mile_assignments')->insertGetId([
        'logistics_account_id' => $logisticsId,
        'destination_facility_id' => $facility->id,
        'courier_account_id' => $riderId,
        'assignment_code' => 'LM-FIN-'.fake()->unique()->numerify('#####'),
        'delivery_area' => 'Test Barangay, Imus',
        'status' => 'completed',
        'assigned_at' => now(),
        'started_at' => now(),
        'completed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('last_mile_assignment_orders')->insert([
        'last_mile_assignment_id' => $assignmentId,
        'marketplace_order_id' => $orderId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $facility;
}

test('COD logistics cash remains pending until SARI Admin receives the exact physical amount and releases all wallets once', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $pickupRider = sariFinanceCourier();
    $pickupRider->forceFill(['logistics_account_id' => $logistics->id])->save();
    $finalRider = sariFinanceCourier();
    $finalRider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder(
        $seller,
        subtotal: 1000.00,
        deliveredAt: now(),
        courier: $pickupRider
    );

    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'original_delivery_fee' => 100.00,
        'delivery_fee' => 100.00,
        'total' => 1100.00,
    ])->save();

    LogisticsParcel::query()->create([
        'marketplace_order_id' => $order->id,
        'status' => 'delivered',
        'received_at' => now(),
        'sorted_at' => now(),
    ]);

    $cashHub = attachLastMile($logistics->id, $finalRider->id, $order->id);

    $result = app(FinancialFlowService::class)->recordCompletedOrder(
        $order->fresh(),
        'test_delivery',
        $finalRider->id
    );

    $pickup = RiderEarning::query()
        ->where('marketplace_order_id', $order->id)
        ->where('earning_type', 'pickup_leg')
        ->firstOrFail();
    $lastMile = RiderEarning::query()
        ->where('marketplace_order_id', $order->id)
        ->where('earning_type', 'last_mile_leg')
        ->firstOrFail();
    $logisticsEarning = LogisticsEarning::query()
        ->where('marketplace_order_id', $order->id)
        ->firstOrFail();

    expect((float) $result['commission']->net_commission)->toBe(100.0)
        ->and((float) $result['seller_settlement']->seller_net_amount)->toBe(900.0)
        ->and((float) $pickup->delivery_fee_amount)->toBe(25.0)
        ->and((float) $lastMile->delivery_fee_amount)->toBe(45.0)
        ->and((float) $logisticsEarning->net_logistics_amount)->toBe(30.0)
        ->and($pickup->status)->toBe('pending_remittance')
        ->and($lastMile->status)->toBe('pending_remittance')
        ->and($logisticsEarning->status)->toBe('pending_remittance');

    $wallets = app(WalletService::class);
    expect((float) $wallets->platformWallet()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $pickupRider->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->available_balance)->toBe(0.0);

    $remittances = app(CodRemittanceService::class);
    $batch = $remittances->submitBatch(
        $finalRider,
        [$order->id],
        null,
        'cash_hub',
        null,
        'Physical cash test batch',
        $cashHub->id
    );

    $verified = $remittances->verifyBatch(
        $batch,
        $logistics,
        1100.00,
        'logistics_manager',
        $logistics->id,
        'TEST-VERIFY-001'
    );

    // Even an exact direct Rider -> Main Branch count is still Logistics custody.
    expect($verified->status)->toBe('verified')
        ->and($verified->central_reconciled_at)->not->toBeNull()
        ->and((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->fresh()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->fresh()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->fresh()->available_balance)->toBe(0.0);

    expect(fn () => app(FinanceReleaseService::class)->releaseOrder($order->fresh()))
        ->toThrow(ValidationException::class);

    $admin = financeAdmin();
    $turnovers = app(AdminCashTurnoverService::class);
    $handover = $turnovers->prepare($logistics, $cashHub, [$verified->id], 'Sealed envelope direct from Main');

    expect($handover->status)->toBe('submitted')
        ->and((float) $handover->expected_amount)->toBe(1100.0)
        ->and((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(0.0);

    $received = $turnovers->receive($handover, $admin, 1100.00, 'Full physical cash counted');

    expect($received->status)->toBe('received')
        ->and($received->received_by_admin_account_id)->toBe($admin->id)
        ->and((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(100.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->fresh()->available_balance)->toBe(900.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->fresh()->available_balance)->toBe(30.0)
        ->and((float) $wallets->wallet('courier', $pickupRider->id)->fresh()->available_balance)->toBe(25.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->fresh()->available_balance)->toBe(45.0);

    app(FinanceReleaseService::class)->releaseOrder($order->fresh(), 'idempotency_recheck');

    expect((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(100.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->fresh()->available_balance)->toBe(900.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->fresh()->available_balance)->toBe(30.0)
        ->and((float) $wallets->wallet('courier', $pickupRider->id)->fresh()->available_balance)->toBe(25.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->fresh()->available_balance)->toBe(45.0);
});

test('delivered COD earnings cannot bypass Main Branch cash reconciliation', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $pickupRider = sariFinanceCourier();
    $pickupRider->forceFill(['logistics_account_id' => $logistics->id])->save();
    $finalRider = sariFinanceCourier();
    $finalRider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder($seller, subtotal: 1000.00, deliveredAt: now(), courier: $pickupRider);
    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'original_delivery_fee' => 100.00,
        'delivery_fee' => 100.00,
        'total' => 1100.00,
    ])->save();

    LogisticsParcel::query()->create([
        'marketplace_order_id' => $order->id,
        'status' => 'delivered',
        'received_at' => now(),
        'sorted_at' => now(),
    ]);

    $regularBranch = attachLastMile($logistics->id, $finalRider->id, $order->id, false);
    app(FinancialFlowService::class)->recordCompletedOrder($order->fresh(), 'reconciliation_guard_test', $finalRider->id);

    // Delivery and finance recognition alone do not release wallet balances.
    expect(fn () => app(FinanceReleaseService::class)->releaseOrder($order->fresh()))
        ->toThrow(ValidationException::class);

    $remittances = app(CodRemittanceService::class);
    $batch = $remittances->submitBatch($finalRider, [$order->id], null, 'cash_hub', null, null, $regularBranch->id);
    $remittances->verifyBatch($batch, $logistics, 1100.00, 'logistics_manager', $logistics->id);

    // Even an exact Rider -> regular Branch cash count is not central custody.
    expect(fn () => app(FinanceReleaseService::class)->releaseOrder($order->fresh()))
        ->toThrow(ValidationException::class);

    expect((float) app(WalletService::class)->platformWallet()->available_balance)->toBe(0.0)
        ->and((float) app(WalletService::class)->wallet('logistics', $logistics->id)->available_balance)->toBe(0.0);
});

test('regular branch Rider cash requires Branch to Main then Admin handover before wallet release', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $pickupRider = sariFinanceCourier();
    $pickupRider->forceFill(['logistics_account_id' => $logistics->id])->save();
    $finalRider = sariFinanceCourier();
    $finalRider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder(
        $seller,
        subtotal: 1000.00,
        deliveredAt: now(),
        courier: $pickupRider
    );

    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'original_delivery_fee' => 100.00,
        'delivery_fee' => 100.00,
        'total' => 1100.00,
    ])->save();

    LogisticsParcel::query()->create([
        'marketplace_order_id' => $order->id,
        'status' => 'delivered',
        'received_at' => now(),
        'sorted_at' => now(),
    ]);

    $regularBranch = attachLastMile($logistics->id, $finalRider->id, $order->id, false);

    $mainBranch = LogisticsFacility::query()->create([
        'logistics_account_id' => $logistics->id,
        'code' => 'MAIN-'.fake()->unique()->numerify('#####'),
        'name' => 'Finance Main Branch',
        'address' => 'Central Treasury Address',
        'province_name' => 'Cavite',
        'municipality_name' => 'Bacoor',
        'barangay_name' => 'Main Treasury',
        'latitude' => 14.4451,
        'longitude' => 120.9511,
        'is_active' => true,
        'is_main_branch' => true,
    ]);

    app(FinancialFlowService::class)->recordCompletedOrder(
        $order->fresh(),
        'two_stage_cash_test',
        $finalRider->id
    );

    $remittances = app(CodRemittanceService::class);
    $riderBatch = $remittances->submitBatch(
        $finalRider,
        [$order->id],
        null,
        'cash_hub',
        null,
        'Regular branch cash handoff',
        $regularBranch->id
    );

    $branchVerified = $remittances->verifyBatch(
        $riderBatch,
        $logistics,
        1100.00,
        'logistics_manager',
        $logistics->id
    );

    $wallets = app(WalletService::class);

    expect($branchVerified->status)->toBe('verified')
        ->and($branchVerified->central_reconciled_at)->toBeNull()
        ->and((float) $wallets->platformWallet()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $pickupRider->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->available_balance)->toBe(0.0);

    $treasury = app(BranchTreasuryService::class);
    $branchBatch = $treasury->prepareBatch(
        $logistics,
        $regularBranch,
        [$branchVerified->id],
        'logistics_manager',
        $logistics->id,
        'Physical branch to main cash'
    );

    expect((float) $branchBatch->expected_amount)->toBe(1100.0)
        ->and($branchBatch->status)->toBe('submitted')
        ->and((int) $branchBatch->destination_facility_id)->toBe((int) $mainBranch->id);

    $centralVerified = $treasury->verifyAtMainBranch(
        $branchBatch,
        $logistics,
        $mainBranch,
        1100.00,
        'logistics_manager',
        $logistics->id
    );

    expect($centralVerified->status)->toBe('verified')
        ->and($branchVerified->fresh()->central_reconciled_at)->not->toBeNull()
        ->and((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->fresh()->available_balance)->toBe(0.0);

    expect(fn () => app(FinanceReleaseService::class)->releaseOrder($order->fresh()))
        ->toThrow(ValidationException::class);

    $turnover = app(AdminCashTurnoverService::class)->prepare(
        $logistics,
        $mainBranch,
        [$branchVerified->id],
        'Main Branch to SARI Admin physical handover'
    );
    $received = app(AdminCashTurnoverService::class)->receive(
        $turnover,
        financeAdmin(),
        1100.00,
        'SARI Admin confirmed exact physical cash'
    );

    expect($received->status)->toBe('received')
        ->and($branchBatch->items()->firstOrFail()->fresh()->central_released_at)->not->toBeNull()
        ->and((float) $wallets->platformWallet()->fresh()->available_balance)->toBe(100.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->fresh()->available_balance)->toBe(900.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->fresh()->available_balance)->toBe(30.0)
        ->and((float) $wallets->wallet('courier', $pickupRider->id)->fresh()->available_balance)->toBe(25.0)
        ->and((float) $wallets->wallet('courier', $finalRider->id)->fresh()->available_balance)->toBe(45.0);
});

test('COD remittance discrepancy blocks wallet release', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $rider = sariFinanceCourier();
    $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder($seller, subtotal: 500.00, deliveredAt: now(), courier: $rider);
    app(FinancialFlowService::class)->recordCompletedOrder($order);

    $cashHub = LogisticsFacility::query()->create([
        'logistics_account_id' => $logistics->id,
        'code' => 'CASH-'.fake()->unique()->numerify('#####'),
        'name' => 'Cash Reconciliation Hub',
        'address' => 'Physical Cash Hub Address',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Test Barangay',
        'latitude' => 14.4297,
        'longitude' => 120.9367,
        'is_active' => true,
    ]);

    $remittances = app(CodRemittanceService::class);
    $batch = $remittances->submitBatch(
        $rider,
        [$order->id],
        null,
        'cash_hub',
        null,
        null,
        $cashHub->id
    );
    $checked = $remittances->verifyBatch(
        $batch,
        $logistics,
        500.00,
        'logistics_manager',
        $logistics->id
    );

    $wallets = app(WalletService::class);

    expect($checked->status)->toBe('discrepancy')
        ->and((float) $checked->difference_amount)->toBe(-80.0)
        ->and((float) $wallets->platformWallet()->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $rider->id)->available_balance)->toBe(0.0);
});

test('Rider COD remittance rejects digital remittance methods', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $rider = sariFinanceCourier();
    $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder($seller, subtotal: 500.00, deliveredAt: now(), courier: $rider);

    app(CodRemittanceService::class)->submitBatch(
        $rider,
        [$order->id],
        null,
        'gcash'
    );
})->throws(ValidationException::class);

test('withdrawal reserves balance rejects safely and only closes after external reference', function (): void {
    $seller = sariFinanceSeller();
    $admin = financeAdmin();
    $wallets = app(WalletService::class);
    $payouts = app(PayoutService::class);
    $wallet = $wallets->wallet('seller', $seller->id);

    $wallets->credit(
        $wallet,
        100.00,
        'test_credit',
        'test-wallet-credit:'.$seller->id,
        null,
        null,
        null,
        'Test wallet credit.'
    );

    $first = $payouts->request('seller', $seller->id, 60.00, 'gcash', 'Test Seller', '09171234567');

    expect((float) $wallet->fresh()->available_balance)->toBe(40.0)
        ->and((float) $wallet->fresh()->reserved_balance)->toBe(60.0);

    $payouts->reject($first, $admin->id, 'Test rejection');

    expect((float) $wallet->fresh()->available_balance)->toBe(100.0)
        ->and((float) $wallet->fresh()->reserved_balance)->toBe(0.0);

    $second = $payouts->request('seller', $seller->id, 100.00, 'bank', 'Test Seller', '1234567890');
    $payouts->approve($second, $admin->id, 'Verified destination');
    $payouts->markPaid($second->fresh(), $admin->id, 'BANK-REF-001');

    expect((float) $wallet->fresh()->available_balance)->toBe(0.0)
        ->and((float) $wallet->fresh()->reserved_balance)->toBe(0.0)
        ->and((float) $wallet->fresh()->lifetime_payouts)->toBe(100.0)
        ->and($second->fresh()->status)->toBe('paid')
        ->and($second->fresh()->external_reference)->toBe('BANK-REF-001');
});

test('withdrawal cannot reserve more than the available wallet balance', function (): void {
    $seller = sariFinanceSeller();
    $wallets = app(WalletService::class);
    $wallet = $wallets->wallet('seller', $seller->id);

    $wallets->credit(
        $wallet,
        50.00,
        'test_credit',
        'test-wallet-limit-credit:'.$seller->id
    );

    app(PayoutService::class)->request(
        'seller',
        $seller->id,
        60.00,
        'gcash',
        'Test Seller',
        '09171234567'
    );
})->throws(ValidationException::class);

test('a wallet idempotency key cannot credit a different owner or amount', function (): void {
    $wallets = app(WalletService::class);
    $sellerA = sariFinanceSeller();
    $sellerB = sariFinanceSeller();
    $walletA = $wallets->wallet('seller', $sellerA->id);
    $walletB = $wallets->wallet('seller', $sellerB->id);

    $wallets->credit($walletA, 100.00, 'test_credit', 'same-key-test');
    $wallets->credit($walletA, 100.00, 'test_credit', 'same-key-test');

    expect((float) $walletA->fresh()->available_balance)->toBe(100.0)
        ->and(fn () => $wallets->credit($walletB, 100.00, 'test_credit', 'same-key-test'))
        ->toThrow(ValidationException::class);

    expect(fn () => $wallets->credit($walletA, 200.00, 'test_credit', 'same-key-test'))
        ->toThrow(ValidationException::class);

    expect((float) $walletB->fresh()->available_balance)->toBe(0.0);
});

test('provision-wallets command is idempotent and never invents earnings', function (): void {
    $seller = sariFinanceSeller();
    $rider = sariFinanceCourier();
    $logistics = financeLogistics();

    $this->artisan('sari:finance:provision-wallets')->assertExitCode(0);
    $firstCount = \App\Models\Finance\FinanceWallet::query()->count();
    $this->artisan('sari:finance:provision-wallets')->assertExitCode(0);

    $wallets = app(WalletService::class);

    expect(\App\Models\Finance\FinanceWallet::query()->count())->toBe($firstCount)
        ->and((float) $wallets->wallet('seller', $seller->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('courier', $rider->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->available_balance)->toBe(0.0)
        ->and((float) $wallets->platformWallet()->available_balance)->toBe(0.0);
});

test('buyers cannot request earnings withdrawals from an internal wallet', function (): void {
    app(PayoutService::class)->request('buyer', 1, 100.00, 'gcash', 'Buyer Name', '09171234567');
})->throws(ValidationException::class);

test('legacy paid rider earnings are not offered for COD remittance again', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $rider = sariFinanceCourier();
    $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder($seller, subtotal: 500.00, deliveredAt: now(), courier: $rider);

    RiderEarning::query()->create([
        'marketplace_order_id' => $order->id,
        'courier_account_id' => $rider->id,
        'earning_type' => 'direct_delivery',
        'delivery_fee_amount' => 80.00,
        'currency' => 'PHP',
        'status' => 'paid',
        'earned_at' => $order->delivered_at,
        'paid_at' => now(),
        'metadata' => ['source' => 'legacy_paid_payout'],
    ]);

    $eligible = app(CodRemittanceService::class)->eligibleOrdersForRider($rider);

    expect($eligible->pluck('id')->contains($order->id))->toBeFalse();
});

test('SARI Admin records shortages and overages without releasing a wallet and retains both count attempts', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $rider = sariFinanceCourier();
    $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

    $order = sariFinanceOrder($seller, subtotal: 1000.00, deliveredAt: now(), courier: $rider);
    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'original_delivery_fee' => 100.00,
        'delivery_fee' => 100.00,
        'total' => 1100.00,
    ])->save();

    // Direct-to-main cash handoff; no separate Branch Treasury step required.
    $main = attachLastMile($logistics->id, $rider->id, $order->id);
    app(FinancialFlowService::class)->recordCompletedOrder($order->fresh(), 'admin_count_test', $rider->id);

    $riderCash = app(CodRemittanceService::class)->submitBatch(
        $rider, [$order->id], null, 'cash_hub', null, null, $main->id
    );
    app(CodRemittanceService::class)->verifyBatch(
        $riderCash, $logistics, 1100.00, 'logistics_manager', $logistics->id
    );

    $turnoverService = app(AdminCashTurnoverService::class);
    $handover = $turnoverService->prepare($logistics, $main, [$riderCash->id]);
    $admin = financeAdmin();

    expect(fn () => $turnoverService->receive($handover, $admin, 1000.00))
        ->toThrow(ValidationException::class);

    $short = $turnoverService->receive($handover, $admin, 1000.00, 'Short by PHP 100, investigating sealed envelope');

    expect($short->status)->toBe('discrepancy')
        ->and((float) $short->difference_amount)->toBe(-100.0)
        ->and((float) app(WalletService::class)->platformWallet()->fresh()->available_balance)->toBe(0.0)
        ->and((float) app(WalletService::class)->wallet('seller', $seller->id)->available_balance)->toBe(0.0)
        ->and(AdminCashTurnoverCount::query()->where('admin_cash_turnover_id', $handover->id)->count())->toBe(1);

    expect(fn () => $turnoverService->prepare($logistics, $main, [$riderCash->id]))
        ->toThrow(ValidationException::class);

    $over = $turnoverService->receive($handover, $admin, 1200.00, 'Over by PHP 100, recount required');

    expect($over->status)->toBe('discrepancy')
        ->and((float) $over->difference_amount)->toBe(100.0)
        ->and((float) app(WalletService::class)->platformWallet()->fresh()->available_balance)->toBe(0.0)
        ->and(AdminCashTurnoverCount::query()->where('admin_cash_turnover_id', $handover->id)->count())->toBe(2);

    $received = $turnoverService->receive($handover, $admin, 1100.00, 'Cash finally counted exactly');

    expect($received->status)->toBe('received')
        ->and($received->received_at)->not->toBeNull()
        ->and((float) app(WalletService::class)->platformWallet()->fresh()->available_balance)->toBe(100.0)
        ->and(AdminCashTurnoverCount::query()->where('admin_cash_turnover_id', $handover->id)->count())->toBe(3);

    expect(fn () => $turnoverService->receive($received, $admin, 1100.00))
        ->toThrow(ValidationException::class);

    expect((float) app(WalletService::class)->platformWallet()->fresh()->available_balance)->toBe(100.0);
});

test('Logistics cannot hand over another provider cash or submit from non-main branch', function (): void {
    $seller = sariFinanceSeller();
    $logistics = financeLogistics();
    $otherProvider = financeLogistics();
    $rider = sariFinanceCourier();
    $rider->forceFill(['logistics_account_id' => $logistics->id])->save();
    $order = sariFinanceOrder($seller, subtotal: 400.00, deliveredAt: now(), courier: $rider);
    $order->forceFill(['logistics_account_id' => $logistics->id])->save();
    $main = attachLastMile($logistics->id, $rider->id, $order->id);
    $otherMain = LogisticsFacility::query()->create([
        'logistics_account_id' => $otherProvider->id,
        'code' => 'OTH-'.fake()->unique()->numerify('#####'),
        'name' => 'Other Logistics Main Branch',
        'address' => 'Other Main Branch Address',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Test Barangay',
        'latitude' => 14.4297,
        'longitude' => 120.9367,
        'is_active' => true,
        'is_main_branch' => true,
    ]);
    app(FinancialFlowService::class)->recordCompletedOrder($order->fresh(), 'other_provider_test', $rider->id);
    $remittance = app(CodRemittanceService::class)->submitBatch($rider, [$order->id], null, 'cash_hub', null, null, $main->id);
    app(CodRemittanceService::class)->verifyBatch(
        $remittance, $logistics, (float) $order->total, 'logistics_manager', $logistics->id
    );
    $service = app(AdminCashTurnoverService::class);

    expect(fn () => $service->prepare($otherProvider, $otherMain, [$remittance->id]))
        ->toThrow(ValidationException::class);

    $main->forceFill(['is_main_branch' => false])->save();

    expect(fn () => $service->prepare($logistics, $main->fresh(), [$remittance->id]))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    expect(AdminCashTurnover::query()->count())->toBe(0);
});

test('only authorized SARI treasury Admin can confirm Logistics physical cash', function (): void {
    $admin = financeAdmin();
    $admin->forceFill(['role' => 'staff', 'can_manage_platform_settings' => false])->save();
    $turnoverService = app(AdminCashTurnoverService::class);

    // Permission denial occurs before the service attempts to lock a turnover.
    expect(fn () => $turnoverService->receive(new AdminCashTurnover(), $admin, 1100.00))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});
