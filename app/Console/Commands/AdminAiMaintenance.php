<?php

namespace App\Console\Commands;

use App\Models\Platform\AdminAiAction;
use App\Models\Platform\AdminAiThread;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\Ai\AdminAiDataService;
use Illuminate\Console\Command;

class AdminAiMaintenance extends Command
{
    protected $signature = 'admin-ai:maintenance';

    protected $description = 'Prune retained AI conversations and publish deduplicated operational alerts';

    public function handle(AdminAiDataService $data, AdminNotificationService $notifications): int
    {
        $days = max(1, (int) config('admin_ai.retention_days'));
        AdminAiThread::query()->where('updated_at', '<', now()->subDays($days))->delete();
        AdminAiAction::query()->where('status', 'pending')->where('expires_at', '<=', now())->update(['status' => 'expired']);
        // Confirmed proposal records are retained for audit. Only abandoned proposals are pruned.
        AdminAiAction::query()->whereIn('status', ['cancelled', 'expired'])->where('updated_at', '<', now()->subDays($days))->delete();
        if (config('admin_ai.enabled') && config('admin_ai.alerts_enabled')) {
            foreach ($data->alerts()['rows'] as $row) {
                if ((float) $row['value'] <= 0) {
                    continue;
                }
                $notifications->publish(
                    'admin-ai:'.now('Asia/Manila')->format('Y-m-d').':'.sha1($row['label']),
                    'operations', 'SARI operational attention', $row['label'].': '.$row['value'], 'admin.monitoring'
                );
            }
        }
        $this->info('AI maintenance completed.');

        return self::SUCCESS;
    }
}
