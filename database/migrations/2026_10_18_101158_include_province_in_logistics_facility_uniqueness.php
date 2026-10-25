<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_UNIQUE = 'logistics_facilities_provider_municipality_unique';

    private const NEW_UNIQUE = 'logistics_facilities_provider_province_municipality_unique';

    /*
     * Keep a dedicated single-column index for the logistics_account_id
     * foreign key before replacing the old composite unique index.
     *
     * MySQL may use OLD_UNIQUE as the supporting index for the foreign key.
     * Dropping OLD_UNIQUE without another suitable index therefore raises
     * error 1553: "Cannot drop index ... needed in a foreign key constraint".
     */
    private const PROVIDER_FK_INDEX = 'lg_facilities_provider_fk_idx';

    public function up(): void
    {
        if (! Schema::hasTable('logistics_facilities')) {
            return;
        }

        if (! Schema::hasIndex('logistics_facilities', self::PROVIDER_FK_INDEX)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->index(
                    ['logistics_account_id'],
                    self::PROVIDER_FK_INDEX
                );
            });
        }

        if (Schema::hasIndex('logistics_facilities', self::OLD_UNIQUE)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->dropUnique(self::OLD_UNIQUE);
            });
        }

        if (! Schema::hasIndex('logistics_facilities', self::NEW_UNIQUE)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->unique(
                    [
                        'logistics_account_id',
                        'province_name',
                        'municipality_name',
                    ],
                    self::NEW_UNIQUE
                );
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('logistics_facilities')) {
            return;
        }

        /*
         * Keep the dedicated provider index in place while switching the
         * uniqueness rule back so the foreign key remains valid throughout.
         */
        if (! Schema::hasIndex('logistics_facilities', self::PROVIDER_FK_INDEX)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->index(
                    ['logistics_account_id'],
                    self::PROVIDER_FK_INDEX
                );
            });
        }

        if (Schema::hasIndex('logistics_facilities', self::NEW_UNIQUE)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->dropUnique(self::NEW_UNIQUE);
            });
        }

        if (! Schema::hasIndex('logistics_facilities', self::OLD_UNIQUE)) {
            Schema::table('logistics_facilities', function (Blueprint $table): void {
                $table->unique(
                    [
                        'logistics_account_id',
                        'municipality_name',
                    ],
                    self::OLD_UNIQUE
                );
            });
        }
    }
};
