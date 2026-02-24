@extends('layouts.app')
@section('title', 'Report Incident')
@section('page-title', 'Report an Incident')
@section('page-subtitle', 'Document an issue encountered during your shift')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        @if($errors->any())
        <div class="alert-error mb-5">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('incidents.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="form-label">Incident Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input"
                    placeholder="e.g. SMS delivery failure on TransPak route" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Severity <span class="text-red-500">*</span></label>
                    <select name="severity" class="form-input" required>
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Resolution Status <span class="text-red-500">*</span></label>
                    <select name="resolution_status" class="form-input" required id="resolution_status" onchange="toggleEscalation()">
                        <option value="resolved" {{ old('resolution_status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="unresolved" {{ old('resolution_status') === 'unresolved' ? 'selected' : '' }}>Unresolved</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" class="form-input" required
                    placeholder="Describe the incident in detail...">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">Steps Taken</label>
                <textarea name="steps_taken" rows="3" class="form-input"
                    placeholder="What actions did you take to address the issue?">{{ old('steps_taken') }}</textarea>
            </div>
            <div id="escalation_section" class="{{ old('resolution_status') === 'unresolved' ? '' : 'hidden' }}">
                <label class="form-label">Escalation Note for Next Shift</label>
                <textarea name="escalation_note" rows="3" class="form-input border-amber-300 focus:border-amber-500 focus:ring-amber-500"
                    placeholder="What does the next shift need to know or do to resolve this?">{{ old('escalation_note') }}</textarea>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400 bg-slate-50 border border-slate-100 rounded-md px-3.5 py-2.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Auto-captured: <strong class="text-slate-600">{{ auth()->user()->name }}</strong>
                &bull; {{ ucfirst(auth()->user()->shift ?? 'morning') }} shift
                &bull; <span class="font-mono">{{ now()->format('H:i, d M Y') }}</span>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('incidents.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Submit Report</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleEscalation() {
    const status = document.getElementById('resolution_status').value;
    const section = document.getElementById('escalation_section');
    section.classList.toggle('hidden', status !== 'unresolved');
}
</script>
@endsection
