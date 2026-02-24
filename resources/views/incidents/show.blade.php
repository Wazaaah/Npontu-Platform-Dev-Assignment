@extends('layouts.app')
@section('title', 'Incident Details')
@section('page-title', 'Incident Details')

@section('content')
<div class="max-w-2xl space-y-4">
    <div class="card overflow-hidden">
        {{-- Severity stripe --}}
        <div class="h-1 w-full {{ match($incident->severity) {
            'critical' => 'bg-red-500',
            'high'     => 'bg-orange-400',
            'medium'   => 'bg-yellow-400',
            default    => 'bg-blue-400'
        } }}"></div>

        <div class="p-6 space-y-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 leading-snug">{{ $incident->title }}</h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Reported by
                        <span class="font-medium text-slate-600">{{ $incident->reporter->name }}</span>
                        &bull; <span class="font-mono">{{ $incident->created_at->format('H:i, d M Y') }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                        {{ match($incident->severity) {
                            'critical' => 'bg-red-100 text-red-700 border border-red-200',
                            'high'     => 'bg-orange-100 text-orange-700 border border-orange-200',
                            'medium'   => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                            default    => 'bg-blue-100 text-blue-700 border border-blue-200'
                        } }}">
                        {{ ucfirst($incident->severity) }}
                    </span>
                    <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }}">
                        {{ ucfirst($incident->resolution_status) }}
                    </span>
                </div>
            </div>

            {{-- Meta strip --}}
            <div class="grid grid-cols-3 gap-3 bg-slate-50 border border-slate-100 rounded-md p-3 text-xs">
                <div>
                    <p class="text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Shift</p>
                    <span class="inline-flex items-center gap-1.5 font-medium
                        {{ $incident->shift === 'morning' ? 'text-amber-700' : 'text-indigo-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $incident->shift === 'morning' ? 'bg-amber-400' : 'bg-indigo-400' }}"></span>
                        {{ ucfirst($incident->shift) }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Date</p>
                    <p class="font-mono text-slate-700">{{ $incident->incident_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 font-semibold uppercase tracking-wide mb-0.5">Department</p>
                    <p class="text-slate-700">{{ $incident->reporter->department ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Description</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $incident->description }}</p>
            </div>

            {{-- Steps taken --}}
            @if($incident->steps_taken)
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Steps Taken</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $incident->steps_taken }}</p>
            </div>
            @endif

            {{-- Escalation --}}
            @if($incident->escalation_note)
            <div class="bg-amber-50 border border-amber-200 rounded-md p-3.5">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1.5">Escalation Note</p>
                <p class="text-sm text-amber-800 leading-relaxed">{{ $incident->escalation_note }}</p>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-1 border-t border-slate-100">
                <a href="{{ route('incidents.index') }}" class="btn-secondary">Back</a>
                @if(!auth()->user()->isAdmin())
                <a href="{{ route('incidents.edit', $incident) }}" class="btn-primary">Edit Incident</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
