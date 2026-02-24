@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'Full trail of system actions by all users')

@section('content')
<div class="space-y-5">

    <div class="card p-4">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="form-label">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">User</label>
                <select name="user_id" class="form-input">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Action</label>
                <select name="action" class="form-input">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('audit-log.index') }}" class="btn-secondary">Clear</a>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Log Entries</h2>
            <span class="text-xs text-gray-400">{{ $logs->total() }} total entries</span>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $log->created_at->format('d M Y, H:i:s') }}
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-700">
                        {{ $log->user?->name ?? '<system>' }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if(str_starts_with($log->action, 'login') || str_starts_with($log->action, 'logout')) bg-blue-50 text-blue-700
                            @elseif(str_starts_with($log->action, 'incident')) bg-red-50 text-red-700
                            @elseif(str_starts_with($log->action, 'activity')) bg-green-50 text-green-700
                            @elseif(str_starts_with($log->action, 'profile')) bg-purple-50 text-purple-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-700 max-w-md">{{ $log->description }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No log entries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
