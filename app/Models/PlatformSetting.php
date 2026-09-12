<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function valueOf(string $key, mixed $default = null): mixed
    {
        /*
         * Version-aware read path.
         *
         * Every governance version stores a complete settings snapshot.
         * Therefore the latest non-cancelled version whose effective_at has
         * arrived is authoritative without rewriting historical versions.
         */
        if (Schema::hasTable('platform_setting_versions')) {
            $version = PlatformSettingVersion::query()
                ->effectiveAt(now())
                ->orderByDesc('effective_at')
                ->orderByDesc('id')
                ->first();

            $settings = $version?->settings;

            if (is_array($settings) && array_key_exists($key, $settings)) {
                return $settings[$key];
            }
        }

        return static::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function putValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => static::stringValue($value)]
        );
    }

    private static function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}
