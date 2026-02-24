@extends('layouts.app')
@section('title', 'Incident Reports')
@section('page-title', 'Incident Reports')
@section('page-subtitle', 'Issues encountered and their resolution status')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between gap-4">
        <div class="card px-4 py-3 flex-1">
            <form method="GET" class="flex items-center gap-4 flex-wrap">
                <div>
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                        class="form-input" onchange="this.form.submit()">
                </div>

                @if(auth()->user()->isAdmin())
                <div>
                    <label class="form-label">Shift</label>
                    <select name="shift" class="form-input" onchange="this.form.submit()">
                        <option value="all"     {{ $shift === 'all'     ? 'selected' : '' }}>All Shifts</option>
                        <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>Morning</option>
                        <option value="night"   {{ $shift === 'night'   ? 'selected' : '' }}>Night</option>
                    </select>
                </div>
                @else
                <div class="flex items-end pb-0.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        All Shifts
                    </span>
                </div>
                @endif

                <div class="flex items-end pb-0.5 text-sm text-slate-500">
                    <span class="font-semibold text-slate-700">{{ $incidents->count() }}</span>
                    <span class="ml-1">{{ Str::plural('report', $incidents->count()) }}</span>
                </div>
            </form>
        </div>

        @if(!auth()->user()->isAdmin())
        <a href="{{ route('incidents.create') }}" class="btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Report Incident
        </a>
        @endif
    </div>

    {{-- Incident cards --}}
    <div class="space-y-3">
        @forelse($incidents as $incident)
        <div class="card overflow-hidden">
            {{-- Severity stripe --}}
            <div class="flex">
                <div class="w-1 shrink-0 {{ match($incident->severity) {
                    'critical' => 'bg-red-500',
                    'high'     => 'bg-orange-400',
                    'medium'   => 'bg-yellow-400',
                    default    => 'bg-blue-400'
                } }}"></div>
                <div class="flex-1 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">

                            {{-- Title row --}}
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $incident->title }}</h3>

                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                    {{ match($incident->severity) {
                                        'critical' => 'bg-red-100 text-red-700 border border-red-200',
                                        'high'     => 'bg-orange-100 text-orange-700 border border-orange-200',
                                        'medium'   => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                        default    => 'bg-blue-100 text-blue-700 border border-blue-200'
                                    } }}">
                                    {{ ucfirst($incident->severity) }}
                                </span>

                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                                    {{ $incident->shift === 'morning'
                                        ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                        : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $incident->shift === 'morning' ? 'bg-amber-400' : 'bg-indigo-400' }}"></span>
                                    {{ ucfirst($incident->shift) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-400 mb-3">
                                Reported by <span class="font-medium text-slate-600">{{ $incident->reporter->name }}</span>
                                &bull; <span class="font-mono">{{ $incident->created_at->format('H:i, d M Y') }}</span>
                            </p>

                            <p class="text-sm text-slate-600 mb-3 leading-relaxed">{{ $incident->description }}</p>

                            @if($incident->steps_taken)
                            <div class="bg-slate-50 border border-slate-100 rounded-md p-3 mb-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Steps Taken</p>
                                <p class="text-sm text-slate-700">{{ $incident->steps_taken }}</p>
                            </div>
                            @endif

                            @if(!$incident->isResolved() && $incident->escalation_note)
                            <div class="bg-amber-50 border border-amber-200 rounded-md p-3">
                                <p class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide mb-1">Escalation Note</p>
                                <p class="text-sm text-amber-800">{{ $incident->escalation_note }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }}">
                                {{ ucfirst($incident->resolution_status) }}
                            </span>
                            <a href="{{ route('incidents.show', $incident) }}"
                               class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                            @if(!auth()->user()->isAdmin())
                            <a href="{{ route('incidents.edit', $incident) }}"
                               class="text-xs text-slate-500 hover:text-slate-700 hover:underline">Edit</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-16 text-center">
            <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-500">No incidents reported for this date</p>
            <p class="text-xs text-slate-400 mt-1">All clear.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
