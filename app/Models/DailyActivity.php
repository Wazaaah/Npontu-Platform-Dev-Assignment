<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A per-shift instance of an {@see ActivityTemplate}, auto-generated each day.
 *
 * Each record tracks the completion state of one recurring task for a specific
 * shift on a specific date. Rather than mutating the status directly, every
 * status change appends a new {@see ActivityUpdate} record, preserving a full
 * history of who did what and when throughout the shift.
 *
 * @property int            $id
 * @property int            $activity_template_id
 * @property \Carbon\Carbon $activity_date
 * @property string         $shift   'morning' | 'night'
 * @property string         $status  'pending' | 'done'
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read string $status_badge HTML badge markup for the current status.
 */
class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_template_id', 'activity_date', 'shift', 'status',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    /**
     * The template that defines this activity's name and category.
     *
     * @return BelongsTo<ActivityTemplate, DailyActivity>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ActivityTemplate::class, 'activity_template_id');
    }

    /**
     * All status updates for this activity, newest first.
     *
     * @return HasMany<ActivityUpdate>
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ActivityUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * The single most recent update (used for "last updated by" display).
     *
     * @return HasOne<ActivityUpdate>
     */
    public function latestUpdate(): HasOne
    {
        return $this->hasOne(ActivityUpdate::class)->latestOfMany();
    }

    /**
     * Whether this activity has been marked as completed.
     *
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Whether this activity is still awaiting completion.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Inline HTML badge representing the current status.
     *
     * @return string Raw HTML — use {!! !!} in Blade to render unescaped.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status === 'done'
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Done</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
    }
}
