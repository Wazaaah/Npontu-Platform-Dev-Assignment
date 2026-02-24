<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DailyActivity;
use App\Models\ActivityUpdate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Manages the daily activity list for a given shift and date.
 *
 * Staff members can view activities filtered by date and search term, and
 * update each activity's status. Admins can browse any shift's activities
 * read-only but are blocked from submitting status changes.
 *
 * Every status change is recorded as an {@see ActivityUpdate} entry (append-only),
 * preserving the full update history used in the handover view.
 */
class DailyActivityController extends Controller
{
    /**
     * Display the activity list for the resolved shift and date.
     *
     * Admins can switch between shifts via query string; staff are always
     * shown their own shift. An optional search term filters activities by
     * template name.
     *
     * @param  Request $request  Query params: date (Y-m-d), shift (admin only), search (string).
     * @return View
     */
    public function index(Request $request): View
    {
        $user   = Auth::user();
        $date   = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $search = $request->search ?? '';

        // Admin can switch shifts freely; staff are locked to their own shift
        if ($user->isAdmin()) {
            $shift = $request->shift ?? 'morning';
        } else {
            $shift = $user->shift ?? 'morning';
        }

        $query = DailyActivity::with(['template', 'updates.updatedBy'])
            ->whereDate('activity_date', $date)
            ->where('shift', $shift);

        if ($search !== '') {
            $query->whereHas('template', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $activities = $query->get();

        return view('daily-activities.index', compact('activities', 'date', 'shift', 'search'));
    }

    /**
     * Record a status update for a single daily activity.
     *
     * Creates an {@see ActivityUpdate} record (preserving history) and then
     * updates the activity's current status. Only staff members may call this
     * endpoint — admins receive a 403 response.
     *
     * @param  Request       $request        Validated fields: status (pending|done), remark (optional).
     * @param  DailyActivity $dailyActivity  Route-model-bound activity to update.
     * @return RedirectResponse              Redirects back with a success flash message.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 if the user is an admin.
     */
    public function update(Request $request, DailyActivity $dailyActivity): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            abort(403, 'Administrators cannot update activity statuses.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,done',
            'remark' => 'nullable|string|max:1000',
        ]);

        // Append a new history record rather than overwriting the previous state
        ActivityUpdate::create([
            'daily_activity_id' => $dailyActivity->id,
            'updated_by'        => Auth::id(),
            'status'            => $validated['status'],
            'remark'            => $validated['remark'] ?? null,
            'updated_at_time'   => now(),
        ]);

        $dailyActivity->update(['status' => $validated['status']]);

        AuditLog::record(
            'activity.updated',
            'DailyActivity',
            $dailyActivity->id,
            Auth::user()->name . " set \"{$dailyActivity->template->name}\" to {$validated['status']}."
                . ($validated['remark'] ? " Remark: \"{$validated['remark']}\"" : '')
        );

        return redirect()->back()->with('success', 'Activity updated successfully.');
    }
}
