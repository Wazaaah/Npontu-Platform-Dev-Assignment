<?php

namespace App\Policies;

use App\Models\IncidentReport;
use App\Models\User;

/**
 * Authorisation policy for {@see IncidentReport} resources.
 *
 * Admins are observers; all non-admin staff members may edit any incident
 * regardless of who originally filed it. This reflects the cross-shift
 * collaboration model — a night-shift staff member should be able to mark
 * a morning-shift incident as resolved upon completion.
 */
class IncidentReportPolicy
{
    /**
     * Determine whether the user can update the given incident.
     *
     * Any authenticated staff member may update any incident. Admins are
     * explicitly excluded — they view incidents but do not edit them.
     *
     * @param  User           $user     The currently authenticated user.
     * @param  IncidentReport $incident The incident being updated.
     * @return bool           True for staff; false for admins.
     */
    public function update(User $user, IncidentReport $incident): bool
    {
        return !$user->isAdmin();
    }
}
