<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Defines a reusable template for a recurring shift activity.
 *
 * Templates are authored by admins and act as blueprints. Each day when a
 * dashboard is first loaded, {@see DashboardController::generateDailyActivities()}
 * creates one {@see DailyActivity} instance per active template applicable to
 * the current shift, using firstOrCreate to avoid duplicates.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 * @property string      $category          'general' | 'sms' | 'network' | 'server' | 'logs'
 * @property string      $applicable_shift  'morning' | 'night' | 'both'
 * @property bool        $is_active         Inactive templates are skipped during generation.
 * @property int         $created_by        Foreign key to {@see User}.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read string $category_label Human-readable category name.
 * @property-read string $shift_label    Human-readable applicable shift.
 */
class ActivityTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'applicable_shift', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The admin who created this template.
     *
     * @return BelongsTo<User, ActivityTemplate>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All daily activity instances spawned from this template.
     *
     * @return HasMany<DailyActivity>
     */
    public function dailyActivities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }

    /**
     * Human-readable display label for the template's category.
     *
     * @return string e.g. 'SMS', 'Network', 'General'.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'sms'     => 'SMS',
            'network' => 'Network',
            'server'  => 'Server',
            'logs'    => 'Logs',
            default   => 'General',
        };
    }

    /**
     * Human-readable display label for the applicable shift.
     *
     * @return string 'Morning', 'Night', or 'Both Shifts'.
     */
    public function getShiftLabelAttribute(): string
    {
        return match($this->applicable_shift) {
            'morning' => 'Morning',
            'night'   => 'Night',
            default   => 'Both Shifts',
        };
    }
}
