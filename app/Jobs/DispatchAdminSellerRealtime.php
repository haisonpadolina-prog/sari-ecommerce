<?php

namespace App\Jobs;

use App\Events\SellerAccountStatusChanged;
use App\Events\SellerRealtimeAlert;
use App\Models\Accounts\SellerAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchAdminSellerRealtime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 20;

    public function __construct(public int $sellerId, public string $kind, public array $payload)
    {
        // Bulk AI actions can roll back. Never broadcast an uncommitted decision.
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function handle(): void
    {
        $seller = SellerAccount::query()->find($this->sellerId);
        if (! $seller) {
            return;
        }
        $seller->ensureRealtimeToken();
        if ($this->kind === 'account-status') {
            SellerAccountStatusChanged::dispatch($seller, (string) ($this->payload['action'] ?? ''), (string) ($this->payload['message'] ?? ''));
        } elseif ($this->kind === 'alert') {
            SellerRealtimeAlert::dispatch(
                $seller, (string) ($this->payload['type'] ?? 'admin_message'),
                (string) ($this->payload['title'] ?? 'SARI Administrator'),
                (string) ($this->payload['message'] ?? ''),
                $this->payload['product_name'] ?? null,
                isset($this->payload['warning_number']) ? (int) $this->payload['warning_number'] : null,
                $seller->suspended_until?->toIso8601String()
            );
        }
    }
}
