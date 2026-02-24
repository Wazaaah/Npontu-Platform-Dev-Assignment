<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\IncidentReport;
use App\Models\ActivityTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Serves the main dashboard, branching on the authenticated user's role.
 *
 * Admins receive an overview of both shifts' activity completion rates and all
 * today's incidents in a management view. Staff members see their own shift's
 * task list along with a summary of handover items from the previous shift.
 *
 * This controller also owns the daily activity generation logic — it runs on
 * every dashboard load and is idempotent thanks to firstOrCreate.
 */
class DashboardController extends Controller
{
    /**
     * Determine which dashboard to render based on the user's role.
     *
     * @return View
     */
    public function index(): View
    {
        $user  = Auth::user();
        $today = Carbon::today();

        if ($user->isAdmin()) {
            return $this->adminDashboard($today);
        }

        return $this->staffDashboard($user, $today);
    }

    /**
     * Build the management dashboard for admin users.
     *
     * Triggers activity generation for both shifts, then fetches activity
     * completion stats per shift and all today's incidents for the summary
     * table. Admins cannot update activities or file incidents themselves.
     *
     * @param  Carbon $today Today's date, used as the activity_date boundary.
     * @return View   dashboard-admin view with shift stats and incidents.
     */
    private function adminDashboard(Carbon $today): View
    {
        // Generate activities for both shifts so the dashboard is always populated
        $this->generateDailyActivities($today, 'morning');
        $this->generateDailyActivities($today, 'night');

        // Morning shift stats
        $morningActivities = DailyActivity::with(['template', 'latestUpdate.updatedBy'])
            ->whereDate('activity_date', $today)
            ->where('shift', 'morning')
            ->get();

        // Night shift stats
        $nightActivities = DailyActivity::with(['template', 'latestUpdate.updatedBy'])
            ->whereDate('activity_date', $today)
            ->where('shift', 'night')
            ->get();

        // All today's incidents across both shifts
        $todayIncidents = IncidentReport::with('reporter')
            ->whereDate('incident_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Team-wide completion metrics
        $totalStaff      = User::where('role', 'staff')->where('is_active', true)->count();
        $allActivities   = $morningActivities->merge($nightActivities);
        $totalActivities = $allActivities->count();
        $doneActivities  = $allActivities->where('status', 'done')->count();
        $completionRate  = $totalActivities > 0
            ? round(($doneActivities / $totalActivities) * 100, 1)
            : 0;

        return view('dashboard-admin', compact(
            'morningActivities', 'nightActivities', 'todayIncidents',
            'totalStaff', 'totalActivities', 'doneActivities', 'completionRate', 'today'
        ));
    }

    /**
     * Build the operational dashboard for shift staff.
     *
     * Shows the current shift's activity list with completion progress and
     * a handover notice if the previous shift has pending activities or
     * unresolved incidents requiring attention.
     *
     * @param  User   $user  The authenticated staff member.
     * @param  Carbon $today Today's date.
     * @return View   dashboard view with activities, incidents, and handover data.
     */
    private function staffDashboard(User $user, Carbon $today): View
    {
        $shift = $user->shift ?? 'morning';

        $this->generateDailyActivities($today, $shift);

        $todayActivities = DailyActivity::with(['template', 'latestUpdate.updatedBy'])
            ->whereDate('activity_date', $today)
            ->where('shift', $shift)
            ->get();

        $pendingCount   = $todayActivities->where('status', 'pending')->count();
        $doneCount      = $todayActivities->where('status', 'done')->count();
        $totalCount     = $todayActivities->count();
        $completionRate = $totalCount > 0 ? round(($doneCount / $totalCount) * 100, 1) : 0;

        // All incidents for today (cross-shift visibility — staff can resolve any shift's incident)
        $todayIncidents = IncidentReport::with('reporter')
            ->whereDate('incident_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Derive which shift and date to pull handover data from
        $previousShift   = $shift === 'morning' ? 'night' : 'morning';
        $previousDate    = $shift === 'morning' ? $today->copy()->subDay() : $today;

        $handoverPending = DailyActivity::with('template')
            ->whereDate('activity_date', $previousDate)
            ->where('shift', $previousShift)
            ->where('status', 'pending')
            ->get();

        $handoverIncidents = IncidentReport::with('reporter')
            ->whereDate('incident_date', $previousDate)
            ->where('shift', $previousShift)
            ->where('resolution_status', 'unresolved')
            ->get();

        return view('dashboard', compact(
            'todayActivities', 'pendingCount', 'doneCount', 'totalCount', 'completionRate',
            'todayIncidents', 'handoverPending', 'handoverIncidents', 'today', 'shift'
        ));
    }

    /**
     * Ensure daily activity records exist for every active template on a given shift/date.
     *
     * Uses firstOrCreate so calling this multiple times per day is safe — it
     * only inserts records that do not yet exist. Any race-condition duplicate
     * inserts throw a UniqueConstraintViolationException, which is silently
     * swallowed since the competing insert already covers the requirement.
     *
     * @param  Carbon $date  The date for which to generate activities.
     * @param  string $shift 'morning' | 'night'
     * @return void
     */
    private function generateDailyActivities(Carbon $date, string $shift): void
    {
        $templates = ActivityTemplate::where('is_active', true)
            ->where(function ($q) use ($shift) {
                $q->where('applicable_shift', $shift)
                  ->orWhere('applicable_shift', 'both');
            })->get();

        foreach ($templates as $template) {
            try {
                DailyActivity::firstOrCreate([
                    'activity_template_id' => $template->id,
                    'activity_date'        => $date->toDateString(),
                    'shift'                => $shift,
                ], ['status' => 'pending']);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Concurrent request already inserted this record — safe to ignore
            }
        }
    }
}
