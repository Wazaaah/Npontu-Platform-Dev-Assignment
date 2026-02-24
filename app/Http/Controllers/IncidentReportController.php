<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\IncidentReport;
use App\Models\User;
use App\Notifications\IncidentReported;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Manages incident reports across all shifts.
 *
 * Incident visibility is cross-shift: all staff members see all incidents for
 * a given date regardless of which shift filed them. This enables any staff
 * member to update the resolution status of an incident they did not create.
 *
 * Admins have read-only access — they can filter and view but cannot create
 * or edit incidents. High and critical incidents trigger real-time in-app
 * notifications dispatched to all other active users via the database channel.
 */
class IncidentReportController extends Controller
{
    /**
     * List incidents for a given date, with optional shift filtering for admins.
     *
     * Staff always see all incidents for the selected date across both shifts.
     * Admins can additionally filter by shift using the 'shift' query parameter.
     *
     * @param  Request $request  Query params: date (Y-m-d), shift ('morning'|'night'|'all') [admin only].
     * @return View
     */
    public function index(Request $request): View
    {
        $user  = Auth::user();
        $today = Carbon::today();

        if ($user->isAdmin()) {
            // Admin: can filter by date and shift freely
            $date  = $request->date ? Carbon::parse($request->date) : $today;
            $shift = $request->shift ?? 'all';

            $query = IncidentReport::with('reporter')
                ->whereDate('incident_date', $date);

            if ($shift !== 'all') {
                $query->where('shift', $shift);
            }

            $incidents = $query->orderBy('created_at', 'desc')->get();

            return view('incidents.index', compact('incidents', 'date', 'shift'));
        }

        // Staff: see all incidents for the selected date across both shifts
        $date  = $request->date ? Carbon::parse($request->date) : $today;
        $shift = 'all';

        $incidents = IncidentReport::with('reporter')
            ->whereDate('incident_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('incidents.index', compact('incidents', 'date', 'shift'));
    }

    /**
     * Show the incident creation form.
     *
     * @return View
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 if user is admin.
     */
    public function create(): View
    {
        if (Auth::user()->isAdmin()) {
            abort(403, 'Administrators do not log incidents directly.');
        }

        return view('incidents.create');
    }

    /**
     * Persist a new incident report and notify relevant users.
     *
     * After saving the incident, an audit log entry is created. If severity
     * is 'high' or 'critical', an {@see IncidentReported} notification is
     * dispatched to all other active users via the database channel.
     *
     * @param  Request          $request  Validated fields: title, description, steps_taken,
     *                                    resolution_status, escalation_note, severity.
     * @return RedirectResponse           Redirects to the incident index with success message.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 if user is admin.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            abort(403, 'Administrators do not log incidents directly.');
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'steps_taken'       => 'nullable|string',
            'resolution_status' => 'required|in:resolved,unresolved',
            'escalation_note'   => 'nullable|string',
            'severity'          => 'required|in:low,medium,high,critical',
        ]);

        $user = Auth::user();

        $incident = IncidentReport::create(array_merge($validated, [
            'reported_by'   => $user->id,
            'incident_date' => Carbon::today()->toDateString(),
            'shift'         => $user->shift ?? 'morning',
        ]));

        AuditLog::record(
            'incident.created',
            'IncidentReport',
            $incident->id,
            "{$user->name} reported a new {$incident->severity} incident: \"{$incident->title}\"."
        );

        // Notify all other active users about high or critical severity incidents
        if (in_array($incident->severity, ['high', 'critical'])) {
            $incident->load('reporter');
            $recipients = User::where('is_active', true)
                ->where('id', '!=', $user->id)
                ->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new IncidentReported($incident));
            }
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incident report submitted successfully.');
    }

    /**
     * Display the full detail view for a single incident.
     *
     * @param  IncidentReport $incident Route-model-bound incident to display.
     * @return View
     */
    public function show(IncidentReport $incident): View
    {
        $incident->load('reporter');

        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the incident edit form.
     *
     * @param  IncidentReport $incident Route-model-bound incident to edit.
     * @return View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 if user is admin.
     */
    public function edit(IncidentReport $incident): View
    {
        if (Auth::user()->isAdmin()) {
            abort(403, 'Administrators cannot edit incident reports.');
        }

        return view('incidents.edit', compact('incident'));
    }

    /**
     * Apply validated changes to an existing incident report.
     *
     * Any non-admin staff member may update any incident — not just the
     * original reporter. This supports cross-shift resolution workflows.
     * All updates are recorded in the audit log.
     *
     * @param  Request          $request  Validated fields: title, description, steps_taken,
     *                                    resolution_status, escalation_note, severity.
     * @param  IncidentReport   $incident Route-model-bound incident to update.
     * @return RedirectResponse           Redirects to the incident detail view.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 if user is admin.
     */
    public function update(Request $request, IncidentReport $incident): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            abort(403, 'Administrators cannot edit incident reports.');
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'steps_taken'       => 'nullable|string',
            'resolution_status' => 'required|in:resolved,unresolved',
            'escalation_note'   => 'nullable|string',
            'severity'          => 'required|in:low,medium,high,critical',
        ]);

        $incident->update($validated);

        AuditLog::record(
            'incident.updated',
            'IncidentReport',
            $incident->id,
            Auth::user()->name . " updated incident \"{$incident->title}\" → status: {$validated['resolution_status']}."
        );

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident report updated successfully.');
    }
}
