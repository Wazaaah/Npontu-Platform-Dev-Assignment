<?php

namespace App\Http\Controllers;

use App\Models\ActivityTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Admin-only CRUD controller for activity templates.
 *
 * Activity templates define the recurring tasks that are auto-instantiated
 * each day as {@see DailyActivity} records. Only administrators can manage
 * templates; this controller is protected by the 'admin' middleware.
 *
 * Templates are "soft-deactivated" rather than deleted — setting is_active
 * to false prevents future daily instances from being generated while
 * preserving historical data integrity.
 */
class ActivityTemplateController extends Controller
{
    /**
     * List all activity templates, newest first, paginated.
     *
     * @return View
     */
    public function index(): View
    {
        $templates = ActivityTemplate::with('creator')->latest()->paginate(15);

        return view('activity-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     *
     * @return View
     */
    public function create(): View
    {
        return view('activity-templates.create');
    }

    /**
     * Persist a new activity template to the database.
     *
     * The creating admin's ID is automatically recorded in the created_by
     * column. New templates are active by default.
     *
     * @param  Request          $request  Validated fields: name, description, category, applicable_shift.
     * @return RedirectResponse           Redirects to the template index with a success message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'required|in:general,sms,network,server,logs',
            'applicable_shift' => 'required|in:morning,night,both',
        ]);

        ActivityTemplate::create(array_merge($validated, [
            'created_by' => Auth::id(),
            'is_active'  => true,
        ]));

        return redirect()->route('activity-templates.index')
            ->with('success', 'Activity template created successfully.');
    }

    /**
     * Show the edit form for an existing template.
     *
     * @param  ActivityTemplate $activityTemplate Route-model-bound template to edit.
     * @return View
     */
    public function edit(ActivityTemplate $activityTemplate): View
    {
        return view('activity-templates.edit', compact('activityTemplate'));
    }

    /**
     * Apply validated changes to an existing template.
     *
     * @param  Request          $request          Validated fields: name, description, category,
     *                                            applicable_shift, is_active.
     * @param  ActivityTemplate $activityTemplate Route-model-bound template to update.
     * @return RedirectResponse                   Redirects to the template index.
     */
    public function update(Request $request, ActivityTemplate $activityTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'category'         => 'required|in:general,sms,network,server,logs',
            'applicable_shift' => 'required|in:morning,night,both',
            'is_active'        => 'boolean',
        ]);

        $activityTemplate->update($validated);

        return redirect()->route('activity-templates.index')
            ->with('success', 'Activity template updated successfully.');
    }

    /**
     * Deactivate a template so it no longer generates new daily activities.
     *
     * Soft-deactivation preserves existing daily activity records that were
     * created from this template while preventing future instances.
     *
     * @param  ActivityTemplate $activityTemplate Route-model-bound template to deactivate.
     * @return RedirectResponse
     */
    public function destroy(ActivityTemplate $activityTemplate): RedirectResponse
    {
        $activityTemplate->update(['is_active' => false]);

        return redirect()->route('activity-templates.index')
            ->with('success', 'Template deactivated successfully.');
    }

    /**
     * Re-activate a previously deactivated template.
     *
     * Once reactivated, the template will be included in the next daily
     * activity generation cycle (triggered on the next dashboard load).
     *
     * @param  ActivityTemplate $activityTemplate Route-model-bound template to reactivate.
     * @return RedirectResponse
     */
    public function restore(ActivityTemplate $activityTemplate): RedirectResponse
    {
        $activityTemplate->update(['is_active' => true]);

        return redirect()->route('activity-templates.index')
            ->with('success', 'Template reactivated successfully.');
    }
}
