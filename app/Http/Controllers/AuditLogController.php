<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin-only read-only view of the audit log.
 *
 * Displays a paginated, filterable list of all recorded system events —
 * logins, logouts, incident CRUD, activity updates, and profile changes.
 * Protected by the 'admin' middleware; staff cannot access this route.
 */
class AuditLogController extends Controller
{
    /**
     * Render the audit log index with optional filters.
     *
     * Supports filtering by action key, specific user, and calendar date.
     * Results are paginated at 50 records per page and preserve filter
     * params in pagination links via withQueryString().
     *
     * @param  Request $request  Query params: action (string), user_id (int), date (Y-m-d).
     * @return View
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $users   = User::orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        return view('audit-log.index', compact('logs', 'users', 'actions'));
    }
}
