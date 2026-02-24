<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records a cross-shift incident reported by a staff member.
 *
 * Incidents are intentionally visible to all shifts regardless of which shift
 * filed them, enabling cross-team situational awareness. When the severity is
 * 'high' or 'critical', all other active users receive an in-app notification
 * via {@see IncidentReported}. Unresolved incidents carry an escalation note
 * that surfaces prominently in the shift handover view.
 *
 * @property int            $id
 * @property int            $reported_by
 * @property \Carbon\Carbon $incident_date
 * @property string         $shift              'morning' | 'night'
 * @property string         $title
 * @property string         $description
 * @property string|null    $steps_taken        Actions taken during the incident.
 * @property string         $resolution_status  'resolved' | 'unresolved'
 * @property string|null    $escalation_note    Passed to the incoming shift when unresolved.
 * @property string         $severity           'low' | 'medium' | 'high' | 'critical'
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read string $severity_color Tailwind colour name matching the severity level.
 */
class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reported_by', 'incident_date', 'shift', 'title', 'description',
        'steps_taken', 'resolution_status', 'escalation_note', 'severity',
    ];

    protected $casts = [
        'incident_date' => 'date',
    ];

    /**
     * The staff member who filed this incident report.
     *
     * @return BelongsTo<User, IncidentReport>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Whether the incident has been marked as resolved.
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return $this->resolution_status === 'resolved';
    }

    /**
     * Tailwind colour name corresponding to the incident's severity level.
     *
     * Used to construct colour-coded badges and stripes in Blade templates.
     *
     * @return string One of: 'red', 'orange', 'yellow', 'blue'.
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high'     => 'orange',
            'medium'   => 'yellow',
            default    => 'blue',
        };
    }
}
