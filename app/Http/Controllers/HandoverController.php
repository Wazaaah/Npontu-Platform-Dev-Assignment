<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\IncidentReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Produces the shift handover summary view.
 *
 * The handover view is the primary tool used during shift transitions. It
 * aggregates all activities and incidents for a specific shift on a specific
 * date, highlights what is still pending, and surfaces escalation notes
 * attached to unresolved incidents so the incoming shift knows exactly what
 * requires attention.
 *
 * Admins can browse any shift on any date. Staff are locked to their own
 * shift but may still select a date (e.g. to review yesterday's handover).
 */
class HandoverController extends Controller
{
    /**
     * Render the shift handover view for the specified date and shift.
     *
     * Aggregates:
     *  - All daily activities for the shift with full update history.
     *  - All incident reports with reporter details and escalation notes.
     *  - Derived stats: total, done, pending, resolved, unresolved counts.
     *
     * @param  Request $request  Query params: date (Y-m-d), shift ('morning'|'night') [admin only].
     * @return View
     */
    public function index(Request $request): View
    {
        $user  = Auth::user();
        $today = Carbon::today();

        // Admin can browse any shift; staff are locked to their own shift
        if ($user->isAdmin()) {
            $date  = $request->date ? Carbon::parse($request->date) : $today;
            $shift = $request->shift ?? 'morning';
        } else {
            $date  = $request->date ? Carbon::parse($request->date) : $today;
            $shift = $user->shift ?? 'morning';
        }

        $activities = DailyActivity::with(['template', 'updates.updatedBy'])
            ->whereDate('activity_date', $date)
            ->where('shift', $shift)
            ->get();

        $incidents = IncidentReport::with('reporter')
            ->whereDate('incident_date', $date)
            ->where('shift', $shift)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalActivities     = $activities->count();
        $doneActivities      = $activities->where('status', 'done')->count();
        $pendingActivities   = $activities->where('status', 'pending')->count();
        $resolvedIncidents   = $incidents->where('resolution_status', 'resolved')->count();
        $unresolvedIncidents = $incidents->where('resolution_status', 'unresolved')->count();

        return view('handover.index', compact(
            'activities', 'incidents', 'date', 'shift',
            'totalActivities', 'doneActivities', 'pendingActivities',
            'resolvedIncidents', 'unresolvedIncidents'
        ));
    }
}
