@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', $today->format('l, d F Y'))

@section('content')
<div class="space-y-5">

    {{-- Stat row --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-slate-400"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Activities</p>
            <p class="text-2xl font-bold text-slate-900 mt-1.5 font-mono tabular-nums">{{ $totalCount }}</p>
            <p class="text-xs text-slate-400 mt-1">Today&rsquo;s shift</p>
        </div>
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-emerald-500"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1.5 font-mono tabular-nums">{{ $doneCount }}</p>
            <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all"
                     style="width: {{ $completionRate }}%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $completionRate }}% done</p>
        </div>
        <div class="stat-card pl-6">
            <div class="stat-card-accent bg-amber-400"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</p>
            <p class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1.5 font-mono tabular-nums">{{ $pendingCount }}</p>
            <p class="text-xs text-slate-400 mt-1">Need attention</p>
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

    {{-- Handover notice --}}
    @if($handoverPending->count() > 0 || $handoverIncidents->count() > 0)
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <div class="w-8 h-8 bg-amber-100 border border-amber-200 rounded-md flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-900">Handover items from previous shift</p>
            <p class="text-xs text-amber-700 mt-0.5">
                <span class="font-semibold">{{ $handoverPending->count() }}</span> pending {{ Str::plural('activity', $handoverPending->count()) }}
                and <span class="font-semibold">{{ $handoverIncidents->count() }}</span> unresolved {{ Str::plural('incident', $handoverIncidents->count()) }} require your attention.
            </p>
            <a href="{{ route('handover.index') }}"
               class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 mt-1.5 hover:underline">
                Open handover report
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif

    {{-- Main grid --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- Activities --}}
        <div class="col-span-2 card overflow-hidden">
            <div class="card-header">
                <div>
                    <p class="card-title">Today&rsquo;s Activities</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $today->format('d F Y') }} &bull; {{ ucfirst($shift) }} shift</p>
                </div>
                <a href="{{ route('activities.index') }}"
                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                    View all &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($todayActivities as $activity)
                <div class="px-5 py-3 flex items-center gap-4">
                    <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ $activity->status === 'done' ? 'bg-emerald-500' : 'bg-amber-400' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $activity->template->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ ucfirst($activity->template->category) }}
                            @if($activity->latestUpdate)
                            &bull; Updated by
                            <span class="text-slate-600 font-medium">{{ $activity->latestUpdate->updatedBy->name }}</span>
                            at <span class="font-mono">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</span>
                            @endif
                        </p>
                    </div>
                    <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }}">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-slate-400">No activities for this shift yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Incidents --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <p class="card-title">Incidents</p>
                @if(!auth()->user()->isAdmin())
                <a href="{{ route('incidents.create') }}"
                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                    + Report
                </a>
                @endif
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($todayIncidents->take(6) as $incident)
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $incident->title }}</p>
                        <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }} shrink-0">
                            {{ ucfirst($incident->resolution_status) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold
                            {{ match($incident->severity) {
                                'critical' => 'bg-red-100 text-red-700',
                                'high'     => 'bg-orange-100 text-orange-700',
                                'medium'   => 'bg-yellow-100 text-yellow-700',
                                default    => 'bg-blue-100 text-blue-700'
                            } }}">
                            {{ ucfirst($incident->severity) }}
                        </span>
                        <span class="text-[11px] text-slate-400">{{ $incident->reporter->name }}</span>
                        <span class="text-[11px] text-slate-400 font-mono ml-auto">{{ $incident->created_at->format('H:i') }}</span>
                    </div>
                </div>
                @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-slate-400">No incidents today.</p>
                </div>
                @endforelse
            </div>
            @if($todayIncidents->count() > 6)
            <div class="px-4 py-3 border-t border-slate-100">
                <a href="{{ route('incidents.index') }}"
                   class="text-xs font-semibold text-indigo-600 hover:underline">
                    View all {{ $todayIncidents->count() }} incidents &rarr;
                </a>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
