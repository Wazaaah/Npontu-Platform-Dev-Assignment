@extends('layouts.app')
@section('title', 'Edit Template')
@section('page-title', 'Edit Activity Template')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('activity-templates.update', $activityTemplate) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Activity Name</label>
                <input type="text" name="name" value="{{ old('name', $activityTemplate->name) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input">{{ old('description', $activityTemplate->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Category</label>
                    <select name="category" class="form-input" required>
                        @foreach(['general','sms','network','server','logs'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $activityTemplate->category) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Applicable Shift</label>
                    <select name="applicable_shift" class="form-input" required>
                        <option value="both" {{ old('applicable_shift', $activityTemplate->applicable_shift) === 'both' ? 'selected' : '' }}>Both Shifts</option>
                        <option value="morning" {{ old('applicable_shift', $activityTemplate->applicable_shift) === 'morning' ? 'selected' : '' }}>Morning Only</option>
                        <option value="night" {{ old('applicable_shift', $activityTemplate->applicable_shift) === 'night' ? 'selected' : '' }}>Night Only</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $activityTemplate->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600">
                <label for="is_active" class="text-sm text-gray-700">Active (will generate daily activities)</label>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('activity-templates.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
