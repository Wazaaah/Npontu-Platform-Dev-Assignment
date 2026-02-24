<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\IncidentReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Generates activity and incident reports with CSV and PDF export options.
 *
 * All reports are built from a shared data-assembly method and can be
 * rendered as an interactive HTML view, streamed as a CSV download, or
 * served as a print-optimised HTML page for browser-based PDF export.
 *
 * Admins can filter across any shift; staff reports are automatically
 * scoped to their own shift.
 */
class ReportController extends Controller
{
    /**
     * Render the report page or dispatch an export based on the request.
     *
     * Checks for an 'export' query parameter to decide the response format:
     *  - 'csv'  → streamed CSV download
     *  - 'pdf'  → print-optimised HTML for browser save-as-PDF
     *  - absent → interactive Blade view
     *
     * @param  Request                          $request  Query params: start_date, end_date,
     *                                                    shift, category, export.
     * @return View|StreamedResponse|Response
     */
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        if ($request->export === 'csv') {
            return $this->exportCsv($data);
        }

        if ($request->export === 'pdf') {
            return $this->exportPdf($data);
        }

        return view('reports.index', $data);
    }

    /**
     * Query and aggregate all report data from the database.
     *
     * Applies date range, shift, and category filters, then calculates
     * summary statistics (total, completed, pending, resolved, unresolved,
     * and completion rate). The returned array is passed directly to views
     * and export methods via compact().
     *
     * @param  Request  $request  The current HTTP request containing filter params.
     * @return array{
     *     activities: \Illuminate\Support\Collection,
     *     incidents: \Illuminate\Support\Collection,
     *     startDate: Carbon,
     *     endDate: Carbon,
     *     shift: string,
     *     category: string,
     *     totalActivities: int,
     *     completedActivities: int,
     *     pendingActivities: int,
     *     totalIncidents: int,
     *     resolvedIncidents: int,
     *     unresolvedIncidents: int,
     *     completionRate: float
     * }
     */
    private function buildReportData(Request $request): array
    {
        $user      = Auth::user();
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today()->subDays(7);
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::today();
        $category  = $request->category ?? 'all';

        // Admins can report across any shift; staff are scoped to their own
        if ($user->isAdmin()) {
            $shift = $request->shift ?? 'all';
        } else {
            $shift = $user->shift ?? 'morning';
        }

        $activityQuery = DailyActivity::with(['template', 'updates.updatedBy'])
            ->whereBetween('activity_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($shift !== 'all') {
            $activityQuery->where('shift', $shift);
        }

        if ($category !== 'all') {
            $activityQuery->whereHas('template', fn($q) => $q->where('category', $category));
        }

        $activities = $activityQuery->orderBy('activity_date', 'desc')->get();

        $incidentQuery = IncidentReport::with('reporter')
            ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($shift !== 'all') {
            $incidentQuery->where('shift', $shift);
        }

        $incidents = $incidentQuery->orderBy('incident_date', 'desc')->get();

        $totalActivities     = $activities->count();
        $completedActivities = $activities->where('status', 'done')->count();
        $pendingActivities   = $activities->where('status', 'pending')->count();
        $totalIncidents      = $incidents->count();
        $resolvedIncidents   = $incidents->where('resolution_status', 'resolved')->count();
        $unresolvedIncidents = $incidents->where('resolution_status', 'unresolved')->count();

        $completionRate = $totalActivities > 0
            ? round(($completedActivities / $totalActivities) * 100, 1)
            : 0;

        return compact(
            'activities', 'incidents', 'startDate', 'endDate', 'shift', 'category',
            'totalActivities', 'completedActivities', 'pendingActivities',
            'totalIncidents', 'resolvedIncidents', 'unresolvedIncidents', 'completionRate'
        );
    }

    /**
     * Stream the report data as a UTF-8 CSV download.
     *
     * Writes two labelled sections — activities and incidents — to a single
     * file. Uses PHP's native fputcsv via php://output for memory-efficient
     * streaming without building the entire string in memory first.
     *
     * @param  array            $data  The assembled report data from buildReportData().
     * @return StreamedResponse        Triggers a file download in the browser.
     */
    private function exportCsv(array $data): StreamedResponse
    {
        $filename = 'report_' . $data['startDate']->format('Ymd') . '_' . $data['endDate']->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $out = fopen('php://output', 'w');

            // Activities section
            fputcsv($out, ['--- ACTIVITY REPORT ---']);
            fputcsv($out, ['Date', 'Shift', 'Activity', 'Category', 'Status', 'Updates', 'Last Updated By', 'Last Update Time']);

            foreach ($data['activities'] as $activity) {
                fputcsv($out, [
                    $activity->activity_date->format('d M Y'),
                    ucfirst($activity->shift),
                    $activity->template->name,
                    $activity->template->category_label,
                    ucfirst($activity->status),
                    $activity->updates->count(),
                    $activity->latestUpdate?->updatedBy->name ?? '—',
                    $activity->latestUpdate?->updated_at_time->format('H:i, d M Y') ?? '—',
                ]);
            }

            fputcsv($out, []);

            // Incidents section
            fputcsv($out, ['--- INCIDENT REPORT ---']);
            fputcsv($out, ['Date', 'Shift', 'Title', 'Severity', 'Status', 'Reported By']);

            foreach ($data['incidents'] as $incident) {
                fputcsv($out, [
                    $incident->incident_date->format('d M Y'),
                    ucfirst($incident->shift),
                    $incident->title,
                    ucfirst($incident->severity),
                    ucfirst($incident->resolution_status),
                    $incident->reporter->name,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Render the print-optimised HTML view for browser-based PDF export.
     *
     * Returns a standalone HTML page (no layout) styled with print CSS and
     * a "Print / Save as PDF" button. The user triggers their browser's
     * print dialog to save the document as a PDF file.
     *
     * @param  array    $data  The assembled report data from buildReportData().
     * @return Response        HTML response with Content-Type: text/html.
     */
    private function exportPdf(array $data): Response
    {
        $html = view('reports.pdf', $data)->render();

        return response($html, 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
