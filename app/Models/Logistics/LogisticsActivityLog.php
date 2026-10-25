<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class LogisticsActivityLog extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'logistics_activity_logs';

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    |
    | Activity records only require created_at.
    | No updated_at column is needed because activity
    | history should not be modified.
    |
    | The logging service supplies created_at.
    |
    */

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'logistics_account_id',

        'actor_type',
        'actor_id',
        'actor_name',

        'logistics_facility_id',
        'facility_name',

        'module',
        'action',
        'description',

        'subject_type',
        'subject_id',

        'created_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'id' => 'integer',

        'logistics_account_id' => 'integer',
        'logistics_facility_id' => 'integer',

        'actor_id' => 'integer',
        'subject_id' => 'integer',

        'created_at' => 'datetime',
    ];
}
