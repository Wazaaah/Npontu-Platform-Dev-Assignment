<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report — Npontu Technologies</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

        .header { background: #111827; color: #fff; padding: 20px 28px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 16px; font-weight: 700; }
        .header p  { font-size: 11px; color: #9ca3af; margin-top: 2px; }
        .header .meta { text-align: right; font-size: 11px; color: #9ca3af; }

        .section { padding: 20px 28px; }
        .section-title { font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #e5e7eb; }

        .stats { display: flex; gap: 12px; margin-bottom: 20px; }
        .stat { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; flex: 1; text-align: center; }
        .stat .val  { font-size: 22px; font-weight: 700; color: #111827; }
        .stat .lbl  { font-size: 10px; color: #6b7280; margin-top: 2px; text-transform: uppercase; }
        .stat.green .val { color: #16a34a; }
        .stat.amber .val { color: #d97706; }
        .stat.red   .val { color: #dc2626; }
        .stat.blue  .val { color: #2563eb; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead tr { background: #f3f4f6; }
        th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; color: #374151; }
        tr:last-child td { border-bottom: none; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-done     { background: #dcfce7; color: #166534; }
        .badge-pending  { background: #fef9c3; color: #854d0e; }
        .badge-resolved   { background: #dcfce7; color: #166534; }
        .badge-unresolved { background: #fee2e2; color: #991b1b; }
        .badge-critical { background: #fee2e2; color: #991b1b; }
        .badge-high     { background: #ffedd5; color: #9a3412; }
        .badge-medium   { background: #fef9c3; color: #854d0e; }
        .badge-low      { background: #dbeafe; color: #1e40af; }

        .footer { border-top: 1px solid #e5e7eb; padding: 12px 28px; display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#1d4ed8;color:#fff;padding:10px 28px;font-size:12px;display:flex;justify-content:space-between;align-items:center;">
    <span>Print or save as PDF using your browser's Print function (Ctrl+P / Cmd+P)</span>
    <button onclick="window.print()" style="background:#fff;color:#1d4ed8;border:none;padding:5px 14px;border-radius:4px;font-weight:600;cursor:pointer;">Print / Save PDF</button>
</div>

<div class="header">
    <div>
        <h1>Activity &amp; Incident Report</h1>
        <p>Npontu Technologies &mdash; Applications Support Team</p>
    </div>
    <div class="meta">
        <p>Period: {{ $startDate->format('d M Y') }} &mdash; {{ $endDate->format('d M Y') }}</p>
        <p>Shift: {{ $shift === 'all' ? 'All Shifts' : ucfirst($shift) }}</p>
        <p>Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>
</div>

<div class="section">
    <p class="section-title">Summary</p>
    <div class="stats">
        <div class="stat"><div class="val">{{ $totalActivities }}</div><div class="lbl">Activities</div></div>
        <div class="stat green"><div class="val">{{ $completedActivities }}</div><div class="lbl">Completed</div></div>
        <div class="stat amber"><div class="val">{{ $pendingActivities }}</div><div class="lbl">Pending</div></div>
        <div class="stat blue"><div class="val">{{ $completionRate }}%</div><div class="lbl">Completion Rate</div></div>
        <div class="stat"><div class="val">{{ $totalIncidents }}</div><div class="lbl">Incidents</div></div>
        <div class="stat red"><div class="val">{{ $unresolvedIncidents }}</div><div class="lbl">Unresolved</div></div>
    </div>
</div>

@if($activities->count())
<div class="section">
    <p class="section-title">Activity History ({{ $activities->count() }} records)</p>
    <table>
        <thead>
            <tr>
                <th>Date</th><th>Shift</th><th>Activity</th><th>Category</th>
                <th>Status</th><th>Updates</th><th>Last Updated By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->activity_date->format('d M Y') }}</td>
                <td>{{ ucfirst($activity->shift) }}</td>
                <td><strong>{{ $activity->template->name }}</strong></td>
                <td>{{ $activity->template->category_label }}</td>
                <td><span class="badge badge-{{ $activity->status }}">{{ ucfirst($activity->status) }}</span></td>
                <td>{{ $activity->updates->count() }}</td>
                <td>
                    @if($activity->latestUpdate)
                        {{ $activity->latestUpdate->updatedBy->name }}<br>
                        <span style="color:#9ca3af">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</span>
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($incidents->count())
<div class="section">
    <p class="section-title">Incident History ({{ $incidents->count() }} records)</p>
    <table>
        <thead>
            <tr>
                <th>Date</th><th>Shift</th><th>Title</th><th>Severity</th><th>Status</th><th>Reported By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidents as $incident)
            <tr>
                <td>{{ $incident->incident_date->format('d M Y') }}</td>
                <td>{{ ucfirst($incident->shift) }}</td>
                <td><strong>{{ $incident->title }}</strong></td>
                <td><span class="badge badge-{{ $incident->severity }}">{{ ucfirst($incident->severity) }}</span></td>
                <td><span class="badge badge-{{ $incident->resolution_status }}">{{ ucfirst($incident->resolution_status) }}</span></td>
                <td>{{ $incident->reporter->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    <span>Npontu Technologies &mdash; Applications Support Tracker</span>
    <span>Confidential &mdash; Internal Use Only</span>
</div>

</body>
</html>
