<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An immutable audit-trail entry for a status change on a {@see DailyActivity}.
 *
 * Every time a staff member updates an activity's status, a new record is
 * inserted rather than overwriting the previous one. This preserves the full
 * update history — who changed what, when, and with what remark — throughout
 * the shift and across handovers.
 *
 * @property int            $id
 * @property int            $daily_activity_id
 * @property int            $updated_by         Foreign key to {@see User}.
 * @property string         $status             'pending' | 'done'
 * @property string|null    $remark             Optional note from the staff member.
 * @property \Carbon\Carbon $updated_at_time    Explicit timestamp of the update action.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ActivityUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_activity_id', 'updated_by', 'status', 'remark', 'updated_at_time',
    ];

    protected $casts = [
        'updated_at_time' => 'datetime',
    ];

    /**
     * The daily activity this update belongs to.
     *
     * @return BelongsTo<DailyActivity, ActivityUpdate>
     */
    public function dailyActivity(): BelongsTo
    {
        return $this->belongsTo(DailyActivity::class);
    }

    /**
     * The staff member who submitted this update.
     *
     * @return BelongsTo<User, ActivityUpdate>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
