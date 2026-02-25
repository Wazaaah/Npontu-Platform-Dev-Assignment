@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Activity Reports')
@section('page-subtitle', 'Query activity and incident history by custom date range')

@section('content')
<div class="space-y-6">
    <div class="card p-5">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-input">
            </div>
            @if(auth()->user()->isAdmin())
            <div>
                <label class="form-label">Shift</label>
                <select name="shift" class="form-input">
                    <option value="all" {{ $shift === 'all' ? 'selected' : '' }}>All Shifts</option>
                    <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>☀️ Morning</option>
                    <option value="night" {{ $shift === 'night' ? 'selected' : '' }}>🌙 Night</option>
                </select>
            </div>
            @endif
            <div>
                <label class="form-label">Category</label>
                <select name="category" class="form-input">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach(['sms','network','server','logs','general'] as $cat)
                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Generate Report</button>
        </form>
        @if(request()->hasAny(['start_date','end_date','shift','category']))
        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500 font-medium">Export:</span>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Print / Save PDF
            </a>
        </div>
        @endif
        @if(!auth()->user()->isAdmin())
        <p class="text-xs text-gray-400 mt-3">
            Showing data for your
            <span class="font-medium {{ $shift === 'morning' ? 'text-amber-600' : 'text-indigo-600' }}">
                {{ $shift === 'morning' ? '☀️ Morning' : '🌙 Night' }} shift
            </span> only.
        </p>
        @endif
    </div>

    <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalActivities }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Activities</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $completedActivities }}</p>
            <p class="text-xs text-gray-500 mt-1">Completed</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $pendingActivities }}</p>
            <p class="text-xs text-gray-500 mt-1">Pending</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $completionRate }}%</p>
            <p class="text-xs text-gray-500 mt-1">Completion Rate</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $totalIncidents }}</p>
            <p class="text-xs text-gray-500 mt-1">Incidents</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $unresolvedIncidents }}</p>
            <p class="text-xs text-gray-500 mt-1">Unresolved</p>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-semibold text-gray-700">Overall Completion Rate</p>
            <p class="text-sm font-bold text-gray-900">{{ $completionRate }}%</p>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3">
            <div class="h-3 rounded-full transition-all
                {{ $completionRate >= 80 ? 'bg-green-500' : ($completionRate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                style="width: {{ $completionRate }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ $startDate->format('d M Y') }} &mdash; {{ $endDate->format('d M Y') }}</p>
    </div>

    @if($activities->count())
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Activity History ({{ $activities->count() }} records)</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Updates</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Updated By</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @foreach($activities as $activity)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $activity->activity_date->format('d M Y') }}</td>
                    <td class="px-6 py-3 text-xs {{ $activity->shift === 'morning' ? 'text-amber-700' : 'text-indigo-700' }}">
                        {{ $activity->shift === 'morning' ? '☀️' : '🌙' }} {{ ucfirst($activity->shift) }}
                    </td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $activity->template->name }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $activity->template->category_label }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }}">{{ ucfirst($activity->status) }}</span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $activity->updates->count() }}</td>
                    <td class="px-6 py-3">
                        @if($activity->latestUpdate)
                        <p class="text-sm text-gray-700">{{ $activity->latestUpdate->updatedBy->name }}</p>
                        <p class="text-xs text-gray-400">{{ $activity->latestUpdate->updated_at_time->format('H:i') }}</p>
                        @else
                        <span class="text-xs text-gray-300">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($incidents->count())
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Incident History ({{ $incidents->count() }} records)</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Severity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reported By</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @foreach($incidents as $incident)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $incident->incident_date->format('d M Y') }}</td>
                    <td class="px-6 py-3 text-xs {{ $incident->shift === 'morning' ? 'text-amber-700' : 'text-indigo-700' }}">
                        {{ $incident->shift === 'morning' ? '☀️' : '🌙' }} {{ ucfirst($incident->shift) }}
                    </td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                        <a href="{{ route('incidents.show', $incident) }}" class="hover:text-blue-600">{{ $incident->title }}</a>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ match($incident->severity) { 'critical' => 'bg-red-100 text-red-800', 'high' => 'bg-orange-100 text-orange-800', 'medium' => 'bg-yellow-100 text-yellow-800', default => 'bg-blue-100 text-blue-800' } }}">
                            {{ ucfirst($incident->severity) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="{{ $incident->resolution_status === 'resolved' ? 'badge-resolved' : 'badge-unresolved' }}">{{ ucfirst($incident->resolution_status) }}</span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-700">{{ $incident->reporter->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($activities->count() === 0 && $incidents->count() === 0)
    <div class="card p-12 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-sm text-gray-400">No data found for the selected date range and filters.</p>
    </div>
    @endif
</div>
@endsection
