@extends('layouts.app')
@section('title', 'Edit Incident')
@section('page-title', 'Edit Incident Report')

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
        <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Incident Title</label>
                <input type="text" name="title" value="{{ old('title', $incident->title) }}" class="form-input" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-input" required>
                        @foreach(['low','medium','high','critical'] as $s)
                        <option value="{{ $s }}" {{ old('severity', $incident->severity) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Resolution Status</label>
                    <select name="resolution_status" class="form-input" required id="resolution_status" onchange="toggleEscalation()">
                        <option value="resolved" {{ old('resolution_status', $incident->resolution_status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="unresolved" {{ old('resolution_status', $incident->resolution_status) === 'unresolved' ? 'selected' : '' }}>Unresolved</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-input" required>{{ old('description', $incident->description) }}</textarea>
            </div>
            <div>
                <label class="form-label">Steps Taken</label>
                <textarea name="steps_taken" rows="3" class="form-input">{{ old('steps_taken', $incident->steps_taken) }}</textarea>
            </div>
            <div id="escalation_section" class="{{ old('resolution_status', $incident->resolution_status) === 'unresolved' ? '' : 'hidden' }}">
                <label class="form-label">Escalation Note</label>
                <textarea name="escalation_note" rows="3" class="form-input border-amber-300">{{ old('escalation_note', $incident->escalation_note) }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('incidents.show', $incident) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Report</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleEscalation() {
    const status = document.getElementById('resolution_status').value;
    document.getElementById('escalation_section').classList.toggle('hidden', status !== 'unresolved');
}
</script>
@endsection
