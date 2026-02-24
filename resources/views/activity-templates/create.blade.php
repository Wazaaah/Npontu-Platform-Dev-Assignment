@extends('layouts.app')
@section('title', 'New Activity Template')
@section('page-title', 'New Activity Template')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('activity-templates.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="form-label">Activity Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input"
                    placeholder="e.g. Daily SMS Count vs Log Count" required>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input"
                    placeholder="What should the personnel do for this activity?">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Category <span class="text-red-500">*</span></label>
                    <select name="category" class="form-input" required>
                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                        <option value="sms" {{ old('category') === 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="network" {{ old('category') === 'network' ? 'selected' : '' }}>Network</option>
                        <option value="server" {{ old('category') === 'server' ? 'selected' : '' }}>Server</option>
                        <option value="logs" {{ old('category') === 'logs' ? 'selected' : '' }}>Logs</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Applicable Shift <span class="text-red-500">*</span></label>
                    <select name="applicable_shift" class="form-input" required>
                        <option value="both" {{ old('applicable_shift') === 'both' ? 'selected' : '' }}>Both Shifts</option>
                        <option value="morning" {{ old('applicable_shift') === 'morning' ? 'selected' : '' }}>Morning Only</option>
                        <option value="night" {{ old('applicable_shift') === 'night' ? 'selected' : '' }}>Night Only</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('activity-templates.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
