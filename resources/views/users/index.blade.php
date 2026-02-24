@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Manage Users')
@section('page-subtitle', 'Add and manage team personnel accounts')

@section('content')
<div class="space-y-5">
    <div class="flex justify-end">
        <a href="{{ route('users.create') }}" class="btn-primary">+ Add User</a>
    </div>
    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                @if($user->phone)<p class="text-xs text-gray-400">{{ $user->phone }}</p>@endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->shift ? ucfirst($user->shift) : '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->department ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Deactivate this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline">Deactivate</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
