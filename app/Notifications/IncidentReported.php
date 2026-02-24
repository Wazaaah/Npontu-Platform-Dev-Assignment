<?php

namespace App\Notifications;

use App\Models\IncidentReport;
use Illuminate\Notifications\Notification;

/**
 * In-app notification dispatched when a high or critical incident is filed.
 *
 * Delivered via the 'database' channel so recipients see a bell-icon alert
 * in the navigation header without requiring email configuration. The
 * notification is sent to all active users other than the reporter by
 * {@see \App\Http\Controllers\IncidentReportController::store()}.
 */
class IncidentReported extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @param  IncidentReport $incident The newly created incident that triggered this alert.
     */
    public function __construct(public readonly IncidentReport $incident) {}

    /**
     * Specify the delivery channels for this notification.
     *
     * Only the 'database' channel is used — notifications are stored in the
     * notifications table and surfaced through the bell icon in the UI.
     *
     * @param  object $notifiable The user receiving the notification.
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Build the data payload stored in the notifications table.
     *
     * All values are serialised as plain strings/scalars to keep the JSON
     * payload self-contained and readable without re-querying the database.
     *
     * @param  object $notifiable The user receiving the notification.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'incident_id'   => $this->incident->id,
            'title'         => $this->incident->title,
            'severity'      => $this->incident->severity,
            'shift'         => $this->incident->shift,
            'reported_by'   => $this->incident->reporter->name,
            'incident_date' => $this->incident->incident_date->format('d M Y'),
        ];
    }
}
