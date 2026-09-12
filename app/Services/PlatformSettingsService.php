<?php

namespace App\Services;

use App\Models\CommissionAuditLog;
use App\Models\CommissionRate;
use App\Models\PlatformSetting;
use App\Models\PlatformSettingAudit;
use App\Models\PlatformSettingVersion;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlatformSettingsService
{
    public const DEFAULT_MAINTENANCE_MESSAGE =
        'SARI commerce services are temporarily unavailable while maintenance is in progress.';

    public function __construct(
        private readonly CommissionService $commissions
    ) {
    }

    public function currentSnapshot(): array
    {
        return $this->normalize([
            'commission_rate' => (float) $this->commissions
                ->currentRate()
                ->rate_percent,

            'delivery_fee_per_seller' => PlatformSetting::valueOf(
                'delivery_fee_per_seller',
                config('sari_buyer.delivery_fee_per_seller', 80)
            ),

            'checkout_enabled' => PlatformSetting::valueOf(
                'checkout_enabled',
                true
            ),

            'registrations_enabled' => PlatformSetting::valueOf(
                'registrations_enabled',
                true
            ),

            'max_sellers_per_checkout' => PlatformSetting::valueOf(
                'max_sellers_per_checkout',
                0
            ),

            'buyer_cancellation_window_minutes' => PlatformSetting::valueOf(
                'buyer_cancellation_window_minutes',
                0
            ),

            'seller_settlement_hold_days' => PlatformSetting::valueOf(
                'seller_settlement_hold_days',
                0
            ),

            'rider_payout_minimum' => PlatformSetting::valueOf(
                'rider_payout_minimum',
                0
            ),

            'registration_decision_email_enabled' => PlatformSetting::valueOf(
                'registration_decision_email_enabled',
                true
            ),

            'maintenance_mode' => PlatformSetting::valueOf(
                'maintenance_mode',
                false
            ),

            'maintenance_message' => PlatformSetting::valueOf(
                'maintenance_message',
                self::DEFAULT_MAINTENANCE_MESSAGE
            ),
        ]);
    }

    public function labels(): array
    {
        return [
            'commission_rate' => 'Commission Rate',
            'delivery_fee_per_seller' => 'Delivery Fee / Seller',
            'checkout_enabled' => 'Checkout Enabled',
            'registrations_enabled' => 'Registrations Enabled',
            'max_sellers_per_checkout' => 'Max Sellers / Checkout',
            'buyer_cancellation_window_minutes' => 'Buyer Cancellation Window',
            'seller_settlement_hold_days' => 'Seller Settlement Hold',
            'rider_payout_minimum' => 'Minimum Rider Payout',
            'registration_decision_email_enabled' => 'Registration Decision Email',
            'maintenance_mode' => 'Commerce Maintenance Mode',
            'maintenance_message' => 'Maintenance Message',
        ];
    }

    public function activeVersion(): ?PlatformSettingVersion
    {
        return PlatformSettingVersion::query()
            ->with(['admin', 'commissionRate'])
            ->effectiveAt(now())
            ->orderByDesc('effective_at')
            ->orderByDesc('id')
            ->first();
    }

    public function scheduledVersion(): ?PlatformSettingVersion
    {
        return PlatformSettingVersion::query()
            ->with(['admin', 'commissionRate'])
            ->scheduled()
            ->orderBy('effective_at')
            ->orderBy('id')
            ->first();
    }

    public function auditHistory(int $limit = 30)
    {
        return PlatformSettingAudit::query()
            ->with(['admin', 'version'])
            ->latest('id')
            ->limit(max(1, min($limit, 100)))
            ->get();
    }

    public function publish(
        array $settings,
        CarbonInterface $effectiveAt,
        ?int $adminAccountId,
        string $reason
    ): PlatformSettingVersion {
        $reason = trim($reason);

        if (mb_strlen($reason) < 10) {
            throw ValidationException::withMessages([
                'change_reason' => 'Explain the reason for this platform policy change in at least 10 characters.',
            ]);
        }

        $newSnapshot = $this->normalize($settings);
        $oldSnapshot = $this->currentSnapshot();

        $changes = $this->changes($oldSnapshot, $newSnapshot);

        if ($changes === []) {
            throw ValidationException::withMessages([
                'settings' => 'No platform setting was changed.',
            ]);
        }

        $effectiveAt = CarbonImmutable::instance($effectiveAt);
        $now = CarbonImmutable::instance(now());
        $scheduled = $effectiveAt->greaterThan($now->addSeconds(5));

        if ($this->scheduledVersion()) {
            throw ValidationException::withMessages([
                'effective_at' => 'A future platform policy is already scheduled. Cancel it before publishing or scheduling another policy so full-snapshot versions cannot overlap.',
            ]);
        }

        return DB::transaction(function () use (
            $oldSnapshot,
            $newSnapshot,
            $changes,
            $effectiveAt,
            $scheduled,
            $adminAccountId,
            $reason
        ): PlatformSettingVersion {
            $commissionRateId = null;

            if (array_key_exists('commission_rate', $changes)) {
                if ($scheduled) {
                    $commissionRateId = $this
                        ->scheduleCommissionRate(
                            (float) $newSnapshot['commission_rate'],
                            $effectiveAt,
                            $adminAccountId,
                            $reason
                        )
                        ->id;
                } else {
                    $commissionRateId = $this->commissions
                        ->setCurrentRate(
                            (float) $newSnapshot['commission_rate'],
                            $adminAccountId,
                            $reason
                        )
                        ->id;
                }
            }

            $nextVersion = ((int) PlatformSettingVersion::query()->max(
                'version_number'
            )) + 1;

            $version = PlatformSettingVersion::query()->create([
                'version_number' => $nextVersion,
                'settings' => $newSnapshot,
                'effective_at' => $effectiveAt,
                'status' => $scheduled ? 'scheduled' : 'published',
                'change_reason' => $reason,
                'created_by_admin_id' => $adminAccountId,
                'commission_rate_id' => $commissionRateId,
            ]);

            /*
             * The legacy key/value table remains a compatibility mirror only
             * for immediately-effective changes. Version-aware reads are the
             * source of truth for scheduled activation.
             */
            if (!$scheduled) {
                foreach ($newSnapshot as $key => $value) {
                    PlatformSetting::putValue($key, $value);
                }
            }

            foreach ($changes as $key => [$oldValue, $newValue]) {
                PlatformSettingAudit::query()->create([
                    'platform_setting_version_id' => $version->id,
                    'admin_account_id' => $adminAccountId,
                    'setting_key' => $key,
                    'action' => $scheduled
                        ? 'setting_scheduled'
                        : 'setting_published',
                    'old_value' => $this->stringify($oldValue),
                    'new_value' => $this->stringify($newValue),
                    'reason' => $reason,
                    'effective_at' => $effectiveAt,
                    'metadata' => [
                        'version_number' => $nextVersion,
                    ],
                ]);
            }

            return $version->fresh(['admin', 'commissionRate']);
        }, 3);
    }

    public function cancelScheduled(
        PlatformSettingVersion $version,
        ?int $adminAccountId,
        string $reason = 'Scheduled platform policy cancelled by administrator.'
    ): void {
        DB::transaction(function () use (
            $version,
            $adminAccountId,
            $reason
        ): void {
            $locked = PlatformSettingVersion::query()
                ->whereKey($version->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $locked->status !== 'scheduled'
                || !$locked->effective_at
                || !$locked->effective_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'scheduled_version' => 'Only a future scheduled policy can be cancelled.',
                ]);
            }

            if ($locked->commission_rate_id) {
                $rate = CommissionRate::query()
                    ->whereKey($locked->commission_rate_id)
                    ->lockForUpdate()
                    ->first();

                if ($rate && $rate->effective_from?->isFuture()) {
                    $previous = CommissionRate::query()
                        ->where('effective_until', $rate->effective_from)
                        ->where('id', '!=', $rate->id)
                        ->orderByDesc('effective_from')
                        ->lockForUpdate()
                        ->first();

                    if ($previous) {
                        $previous->forceFill([
                            'effective_until' => null,
                        ])->save();
                    }

                    $cancelledRate = [
                        'id' => $rate->id,
                        'rate_percent' => (float) $rate->rate_percent,
                        'effective_from' => $rate->effective_from?->toIso8601String(),
                    ];

                    $rate->delete();

                    CommissionAuditLog::query()->create([
                        'commission_rate_id' => null,
                        'admin_account_id' => $adminAccountId,
                        'actor_type' => $adminAccountId ? 'admin' : 'system',
                        'action' => 'commission_rate_schedule_cancelled',
                        'description' => $reason,
                        'old_values' => $cancelledRate,
                        'new_values' => null,
                        'metadata' => [
                            'platform_setting_version_id' => $locked->id,
                        ],
                    ]);
                }
            }

            $locked->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ])->save();

            PlatformSettingAudit::query()->create([
                'platform_setting_version_id' => $locked->id,
                'admin_account_id' => $adminAccountId,
                'setting_key' => '*',
                'action' => 'scheduled_policy_cancelled',
                'old_value' => 'scheduled',
                'new_value' => 'cancelled',
                'reason' => $reason,
                'effective_at' => $locked->effective_at,
                'metadata' => [
                    'version_number' => $locked->version_number,
                ],
            ]);
        }, 3);
    }

    public function formatValue(string $key, mixed $value): string
    {
        if (in_array($key, [
            'checkout_enabled',
            'registrations_enabled',
            'registration_decision_email_enabled',
            'maintenance_mode',
        ], true)) {
            return $this->toBool($value) ? 'Enabled' : 'Disabled';
        }

        if ($key === 'commission_rate') {
            return number_format((float) $value, 2) . '%';
        }

        if (in_array($key, [
            'delivery_fee_per_seller',
            'rider_payout_minimum',
        ], true)) {
            return '₱' . number_format((float) $value, 2);
        }

        if ($key === 'buyer_cancellation_window_minutes') {
            return (int) $value === 0
                ? 'No time limit while order is New'
                : number_format((int) $value) . ' minutes';
        }

        if ($key === 'max_sellers_per_checkout') {
            return (int) $value === 0
                ? 'Unlimited'
                : number_format((int) $value);
        }

        if ($key === 'seller_settlement_hold_days') {
            return number_format((int) $value) . ' days';
        }

        return (string) $value;
    }

    private function scheduleCommissionRate(
        float $ratePercent,
        CarbonInterface $effectiveAt,
        ?int $adminAccountId,
        string $reason
    ): CommissionRate {
        if ($ratePercent < 0 || $ratePercent > 100) {
            throw ValidationException::withMessages([
                'commission_rate' => 'Commission rate must be between 0 and 100.',
            ]);
        }

        if (CommissionRate::query()
            ->where('effective_from', '>', now())
            ->exists()) {
            throw ValidationException::withMessages([
                'commission_rate' => 'A future commission rate is already scheduled.',
            ]);
        }

        $current = CommissionRate::query()
            ->activeAt(now())
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if (!$current) {
            $current = $this->commissions->currentRate();

            $current = CommissionRate::query()
                ->whereKey($current->id)
                ->lockForUpdate()
                ->firstOrFail();
        }

        $oldValues = [
            'commission_rate_id' => $current->id,
            'rate_percent' => (float) $current->rate_percent,
            'effective_from' => $current->effective_from?->toIso8601String(),
            'effective_until' => $current->effective_until?->toIso8601String(),
        ];

        $current->forceFill([
            'effective_until' => $effectiveAt,
        ])->save();

        $newRate = CommissionRate::query()->create([
            'rate_percent' => round($ratePercent, 4),
            'calculation_basis' => CommissionService::BASIS,
            'effective_from' => $effectiveAt,
            'effective_until' => null,
            'changed_by_admin_id' => $adminAccountId,
            'reason' => $reason,
        ]);

        CommissionAuditLog::query()->create([
            'commission_rate_id' => $newRate->id,
            'admin_account_id' => $adminAccountId,
            'actor_type' => $adminAccountId ? 'admin' : 'system',
            'action' => 'commission_rate_scheduled',
            'description' => $reason,
            'old_values' => $oldValues,
            'new_values' => [
                'commission_rate_id' => $newRate->id,
                'rate_percent' => (float) $newRate->rate_percent,
                'effective_from' => $newRate->effective_from?->toIso8601String(),
                'calculation_basis' => $newRate->calculation_basis,
            ],
        ]);

        return $newRate;
    }

    private function normalize(array $settings): array
    {
        return [
            'commission_rate' => round(
                max(0, min(100, (float) ($settings['commission_rate'] ?? 0))),
                4
            ),

            'delivery_fee_per_seller' => round(
                max(0, (float) ($settings['delivery_fee_per_seller'] ?? 0)),
                2
            ),

            'checkout_enabled' => $this->toBool(
                $settings['checkout_enabled'] ?? true
            ),

            'registrations_enabled' => $this->toBool(
                $settings['registrations_enabled'] ?? true
            ),

            'max_sellers_per_checkout' => max(
                0,
                (int) ($settings['max_sellers_per_checkout'] ?? 0)
            ),

            'buyer_cancellation_window_minutes' => max(
                0,
                (int) ($settings['buyer_cancellation_window_minutes'] ?? 0)
            ),

            'seller_settlement_hold_days' => max(
                0,
                (int) ($settings['seller_settlement_hold_days'] ?? 0)
            ),

            'rider_payout_minimum' => round(
                max(0, (float) ($settings['rider_payout_minimum'] ?? 0)),
                2
            ),

            'registration_decision_email_enabled' => $this->toBool(
                $settings['registration_decision_email_enabled'] ?? true
            ),

            'maintenance_mode' => $this->toBool(
                $settings['maintenance_mode'] ?? false
            ),

            'maintenance_message' => trim(
                (string) (
                    $settings['maintenance_message']
                    ?? self::DEFAULT_MAINTENANCE_MESSAGE
                )
            ) ?: self::DEFAULT_MAINTENANCE_MESSAGE,
        ];
    }

    private function changes(array $old, array $new): array
    {
        $changes = [];

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [$oldValue, $newValue];
            }
        }

        return $changes;
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Enabled' : 'Disabled';
        }

        return (string) $value;
    }

    private function toBool(mixed $value): bool
    {
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
