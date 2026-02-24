<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Immutable record of a significant user action for security and accountability.
 *
 * Audit logs are append-only and should never be updated or deleted after
 * creation. They are viewable only by administrators via {@see AuditLogController}.
 *
 * Actions are stored as dot-separated keys, e.g.:
 *  - 'login' / 'logout'
 *  - 'incident.created' / 'incident.updated'
 *  - 'activity.updated'
 *  - 'profile.updated' / 'profile.password_changed'
 *
 * @property int         $id
 * @property int|null    $user_id      Null if the action was performed by a guest.
 * @property string      $action       Dot-separated event key.
 * @property string|null $entity_type  Short model name (e.g. 'IncidentReport', 'User').
 * @property int|null    $entity_id    Primary key of the affected record.
 * @property string      $description  Human-readable summary of the event.
 * @property string      $ip_address   Client IP address at the time of the action.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id', 'description', 'ip_address',
    ];

    /**
     * The user who performed the audited action, if authenticated.
     *
     * @return BelongsTo<User, AuditLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Append an audit log entry for the currently authenticated user.
     *
     * This is the sole intended way to create audit log records. It
     * automatically resolves the current user ID and client IP address.
     *
     * Example usage:
     * ```php
     * AuditLog::record('incident.created', 'IncidentReport', $incident->id, "User reported: \"{$incident->title}\".");
     * ```
     *
     * @param  string      $action       Dot-separated event key (e.g. 'incident.created').
     * @param  string|null $entityType   Model class name of the affected entity, or null.
     * @param  int|null    $entityId     Primary key of the affected entity, or null.
     * @param  string      $description  Human-readable description of what occurred.
     * @return void
     */
    public static function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        string $description = ''
    ): void {
        static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => $description,
            'ip_address'  => Request::ip(),
        ]);
    }
}
