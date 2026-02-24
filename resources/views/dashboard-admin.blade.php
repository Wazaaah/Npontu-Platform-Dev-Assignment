@extends('layouts.app')
@section('title', 'Management Dashboard')
@section('page-title', 'Management Dashboard')
@section('page-subtitle', $today->format('l, d F Y'))

@section('content')
<div class="space-y-5">

    {{-- Top stats --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-indigo-500"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Active Staff</p>
            <p class="text-2xl font-bold text-slate-900 mt-1.5 font-mono tabular-nums">{{ $totalStaff }}</p>
            <p class="text-xs text-slate-400 mt-1">Across both shifts</p>
        </div>
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-slate-400"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Activities</p>
            <p class="text-2xl font-bold text-slate-900 mt-1.5 font-mono tabular-nums">{{ $totalActivities }}</p>
            <p class="text-xs text-slate-400 mt-1">Both shifts combined</p>
        </div>
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-emerald-500"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1.5 font-mono tabular-nums">{{ $doneActivities }}</p>
            <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $completionRate }}%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $completionRate }}% completion</p>
        </div>
        <div class="stat-card pl-6">
            <div class="stat-card-accent {{ $todayIncidents->where('resolution_status','unresolved')->count() > 0 ? 'bg-red-500' : 'bg-slate-300' }}"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Incidents Today</p>
            <p class="text-2xl font-bold {{ $todayIncidents->count() > 0 ? 'text-red-600' : 'text-slate-900' }} mt-1.5 font-mono tabular-nums">{{ $todayIncidents->count() }}</p>
            <p class="text-xs text-slate-400 mt-1">
                {{ $todayIncidents->where('resolution_status','unresolved')->count() }} unresolved
            </p>
        </div>
    </div>

    {{-- Shift columns --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- Morning --}}
        <div class="card overflow-hidden">
            <div class="card-header bg-amber-50 border-b border-amber-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                    <div>
                        <p class="text-sm font-semibold text-amber-900">Morning Shift</p>
                        <p class="text-[11px] text-amber-600">06:00 &ndash; 18:00</p>
                    </div>
                </div>
                <div class="text-right">
                    @php $morningRate = $morningActivities->count() > 0
                        ? round(($morningActivities->where('status','done')->count() / $morningActivities->count()) * 100)
                        : 0; @endphp
                    <p class="text-xs font-semibold text-amber-800">
                        <span class="text-emerald-700">{{ $morningActivities->where('status','done')->count() }}</span>
                        / {{ $morningActivities->count() }}
                        <span class="font-normal text-amber-600 ml-1">done</span>
                    </p>
                    <div class="w-20 h-1.5 bg-amber-100 rounded-full overflow-hidden mt-1 ml-auto">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $morningRate }}%"></div>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                @forelse($morningActivities as $activity)
                <div class="px-4 py-2.5 flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ $activity->status === 'done' ? 'bg-emerald-500' : 'bg-amber-400' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800 truncate">{{ $activity->template->name }}</p>
                        @if($activity->latestUpdate)
                        <p class="text-[11px] text-slate-400">
                            {{ $activity->latestUpdate->updatedBy->name }}
                            &bull; <span class="font-mono">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</span>
                        </p>
                        @else
                        <p class="text-[11px] text-slate-300">Not updated</p>
                        @endif
                    </div>
                    <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }}">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm text-slate-400">No activities generated yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Night --}}
        <div class="card overflow-hidden">
            <div class="card-header bg-indigo-50 border-b border-indigo-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 shrink-0"></span>
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Night Shift</p>
                        <p class="text-[11px] text-indigo-600">18:00 &ndash; 06:00</p>
                    </div>
                </div>
                <div class="text-right">
                    @php $nightRate = $nightActivities->count() > 0
                        ? round(($nightActivities->where('status','done')->count() / $nightActivities->count()) * 100)
                        : 0; @endphp
                    <p class="text-xs font-semibold text-indigo-800">
                        <span class="text-emerald-700">{{ $nightActivities->where('status','done')->count() }}</span>
                        / {{ $nightActivities->count() }}
                        <span class="font-normal text-indigo-600 ml-1">done</span>
                    </p>
                    <div class="w-20 h-1.5 bg-indigo-100 rounded-full overflow-hidden mt-1 ml-auto">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $nightRate }}%"></div>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                @forelse($nightActivities as $activity)
                <div class="px-4 py-2.5 flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ $activity->status === 'done' ? 'bg-emerald-500' : 'bg-amber-400' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800 truncate">{{ $activity->template->name }}</p>
                        @if($activity->latestUpdate)
                        <p class="text-[11px] text-slate-400">
                            {{ $activity->latestUpdate->updatedBy->name }}
                            &bull; <span class="font-mono">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</span>
                        </p>
                        @else
                        <p class="text-[11px] text-slate-300">Not updated</p>
                        @endif
                    </div>
                    <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }}">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm text-slate-400">No activities generated yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Incidents table --}}
    @if($todayIncidents->count())
    <div class="card overflow-hidden">
        <div class="card-header">
            <p class="card-title">All Incidents Today
                <span class="ml-2 text-xs font-normal text-slate-400">({{ $todayIncidents->count() }})</span>
            </p>
            <a href="{{ route('incidents.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                Full view &rarr;
            </a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Shift</th>
                    <th>Title</th>
                    <th>Severity</th>
                    <th>Reported By</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todayIncidents as $incident)
                <tr>
                    <td>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium
                            {{ $incident->shift === 'morning' ? 'text-amber-700' : 'text-indigo-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $incident->shift === 'morning' ? 'bg-amber-400' : 'bg-indigo-400' }}"></span>
                            {{ ucfirst($incident->shift) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('incidents.show', $incident) }}"
                           class="font-medium text-slate-800 hover:text-indigo-600 transition-colors">
                            {{ $incident->title }}
                        </a>
                    </td>
                    <td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                            {{ match($incident->severity) {
                                'critical' => 'bg-red-100 text-red-700 border border-red-200',
                                'high'     => 'bg-orange-100 text-orange-700 border border-orange-200',
                                'medium'   => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                default    => 'bg-blue-100 text-blue-700 border border-blue-200'
                            } }}">
                            {{ ucfirst($incident->severity) }}
                        </span>
                    </td>
                    <td class="text-slate-600">{{ $incident->reporter->name }}</td>
                    <td class="font-mono text-slate-400">{{ $incident->created_at->format('H:i') }}</td>
                    <td>
                        <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }}">
                            {{ ucfirst($incident->resolution_status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card p-10 text-center">
        <p class="text-sm text-slate-400">No incidents reported today.</p>
    </div>
    @endif

</div>
@endsection
