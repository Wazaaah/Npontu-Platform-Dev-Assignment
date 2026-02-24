<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Represents a system user — either an administrator or shift staff.
 *
 * Admins have no assigned shift and hold elevated privileges (manage users,
 * templates, view audit logs). Staff belong to either the morning or night
 * shift and are responsible for activity tracking and incident reporting.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string      $role        'admin' | 'staff'
 * @property string|null $shift       'morning' | 'night' | null (null for admins)
 * @property string|null $phone
 * @property string|null $department
 * @property bool        $is_active   Inactive users cannot log in.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read string $shift_label Human-readable shift label with hours.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'shift', 'phone', 'department', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    /**
     * Determine whether this user holds the admin role.
     *
     * @return bool True if role === 'admin'.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * All activity status updates submitted by this user.
     *
     * @return HasMany<ActivityUpdate>
     */
    public function activityUpdates(): HasMany
    {
        return $this->hasMany(ActivityUpdate::class, 'updated_by');
    }

    /**
     * All incident reports filed by this user.
     *
     * @return HasMany<IncidentReport>
     */
    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'reported_by');
    }

    /**
     * All activity templates created by this user.
     *
     * @return HasMany<ActivityTemplate>
     */
    public function activityTemplates(): HasMany
    {
        return $this->hasMany(ActivityTemplate::class, 'created_by');
    }

    /**
     * Human-readable label for the user's shift assignment, including hours.
     *
     * @return string e.g. 'Morning (6AM – 6PM)' or 'Not assigned' for admins.
     */
    public function getShiftLabelAttribute(): string
    {
        return match($this->shift) {
            'morning' => 'Morning (6AM – 6PM)',
            'night'   => 'Night (6PM – 6AM)',
            default   => 'Not assigned',
        };
    }
}
