@extends('layouts.app')
@section('title', 'Shift Handover')
@section('page-title', 'Shift Handover')
@section('page-subtitle', 'Complete summary for shift transition')

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <div class="card px-4 py-3">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="form-label">Date</label>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    class="form-input" onchange="this.form.submit()">
            </div>
            @if(auth()->user()->isAdmin())
            <div>
                <label class="form-label">Shift</label>
                <select name="shift" class="form-input" onchange="this.form.submit()">
                    <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>Morning</option>
                    <option value="night"   {{ $shift === 'night'   ? 'selected' : '' }}>Night</option>
                </select>
            </div>
            @else
            <div class="flex items-end pb-0.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $shift === 'morning'
                        ? 'bg-amber-50 text-amber-700 border border-amber-200'
                        : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $shift === 'morning' ? 'bg-amber-400' : 'bg-indigo-400' }}"></span>
                    {{ ucfirst($shift) }} Shift
                </span>
            </div>
            @endif
            <p class="text-sm text-slate-500 pb-0.5 font-mono">{{ $date->format('d M Y') }}</p>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-5 gap-4">
        <div class="stat-card pl-5 text-center">
            <div class="stat-card-accent bg-slate-400"></div>
            <p class="text-2xl font-bold text-slate-900 font-mono tabular-nums">{{ $totalActivities }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Activities</p>
        </div>
        <div class="stat-card pl-5 text-center">
            <div class="stat-card-accent bg-emerald-500"></div>
            <p class="text-2xl font-bold text-emerald-600 font-mono tabular-nums">{{ $doneActivities }}</p>
            <p class="text-xs text-slate-500 mt-1">Completed</p>
        </div>
        <div class="stat-card pl-5 text-center {{ $pendingActivities > 0 ? 'border-amber-300' : '' }}">
            <div class="stat-card-accent {{ $pendingActivities > 0 ? 'bg-amber-400' : 'bg-slate-300' }}"></div>
            <p class="text-2xl font-bold {{ $pendingActivities > 0 ? 'text-amber-600' : 'text-slate-900' }} font-mono tabular-nums">{{ $pendingActivities }}</p>
            <p class="text-xs text-slate-500 mt-1">Pending handover</p>
        </div>
        <div class="stat-card pl-5 text-center">
            <div class="stat-card-accent bg-indigo-400"></div>
            <p class="text-2xl font-bold text-indigo-600 font-mono tabular-nums">{{ $resolvedIncidents }}</p>
            <p class="text-xs text-slate-500 mt-1">Incidents Resolved</p>
        </div>
        <div class="stat-card pl-5 text-center {{ $unresolvedIncidents > 0 ? 'border-red-300' : '' }}">
            <div class="stat-card-accent {{ $unresolvedIncidents > 0 ? 'bg-red-500' : 'bg-slate-300' }}"></div>
            <p class="text-2xl font-bold {{ $unresolvedIncidents > 0 ? 'text-red-600' : 'text-slate-900' }} font-mono tabular-nums">{{ $unresolvedIncidents }}</p>
            <p class="text-xs text-slate-500 mt-1">Incidents Escalated</p>
        </div>
    </div>

    {{-- Activities + Incidents --}}
    <div class="grid grid-cols-2 gap-5">
        <div class="card overflow-hidden">
            <div class="card-header">
                <p class="card-title">Activities Summary</p>
                @if($pendingActivities > 0)
                <span class="badge-pending">{{ $pendingActivities }} pending</span>
                @endif
            </div>
            <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto">
                @forelse($activities as $activity)
                <div class="px-4 py-3 flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0
                        {{ $activity->status === 'done' ? 'bg-emerald-500' : 'bg-amber-400' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $activity->template->name }}</p>
                        @if($activity->latestUpdate)
                        <p class="text-xs text-slate-400 mt-0.5">
                            Last: <span class="text-slate-600 font-medium">{{ $activity->latestUpdate->updatedBy->name }}</span>
                            at <span class="font-mono">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</span>
                            @if($activity->latestUpdate->remark)
                            &mdash; <span class="italic">&ldquo;{{ Str::limit($activity->latestUpdate->remark, 40) }}&rdquo;</span>
                            @endif
                        </p>
                        @else
                        <p class="text-xs text-slate-300">No updates this shift</p>
                        @endif
                    </div>
                    <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }} shrink-0">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>
                @empty
                <div class="px-4 py-10 text-center text-sm text-slate-400">No activities for this shift.</div>
                @endforelse
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <p class="card-title">Incident Reports</p>
                @if($unresolvedIncidents > 0)
                <span class="badge-unresolved">{{ $unresolvedIncidents }} escalated</span>
                @endif
            </div>
            <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto">
                @forelse($incidents as $incident)
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800">{{ $incident->title }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $incident->reporter->name }} &bull;
                                <span class="font-mono">{{ $incident->created_at->format('H:i') }}</span>
                            </p>
                            @if(!$incident->isResolved() && $incident->escalation_note)
                            <p class="text-xs text-amber-700 mt-1 italic">
                                &ldquo;{{ Str::limit($incident->escalation_note, 65) }}&rdquo;
                            </p>
                            @endif
                        </div>
                        <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }} shrink-0">
                            {{ ucfirst($incident->resolution_status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-4 py-10 text-center text-sm text-slate-400">No incidents this shift.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pending activities full history --}}
    @if($activities->where('status', 'pending')->count() > 0)
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-amber-100 bg-amber-50 flex items-center gap-3">
            <div class="w-7 h-7 bg-amber-100 border border-amber-200 rounded-md flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-amber-900">Pending Activities — Full Update History</p>
                <p class="text-xs text-amber-700 mt-0.5">These items need attention from the incoming shift.</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Update History</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @foreach($activities->where('status', 'pending') as $activity)
                <tr class="align-top">
                    <td class="w-56">
                        <p class="font-medium text-slate-800">{{ $activity->template->name }}</p>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-semibold
                                     bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $activity->template->category_label }}
                        </span>
                    </td>
                    <td>
                        @if($activity->updates->count())
                        <div class="space-y-1.5">
                            @foreach($activity->updates as $upd)
                            <div class="flex items-start gap-3 text-xs text-slate-600 pl-3 border-l-2 border-slate-200">
                                <span class="font-semibold {{ $upd->status === 'done' ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ ucfirst($upd->status) }}
                                </span>
                                <span>by <strong class="text-slate-800">{{ $upd->updatedBy->name }}</strong></span>
                                <span class="text-slate-400 font-mono">{{ $upd->updated_at_time->format('H:i') }}</span>
                                @if($upd->remark)
                                <span class="italic text-slate-500">&ldquo;{{ $upd->remark }}&rdquo;</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs text-slate-400">No updates recorded</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
