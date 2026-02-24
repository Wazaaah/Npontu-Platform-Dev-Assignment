@extends('layouts.app')
@section('title', 'Activity Templates')
@section('page-title', 'Activity Templates')
@section('page-subtitle', 'Manage recurring daily activity templates')

@section('content')
<div class="space-y-5">
    <div class="flex justify-end">
        <a href="{{ route('activity-templates.create') }}" class="btn-primary">+ New Template</a>
    </div>
    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Applies To</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50 {{ !$template->is_active ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $template->name }}</p>
                        @if($template->description)
                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($template->description, 60) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $template->category_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $template->shift_label }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $template->creator->name }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('activity-templates.edit', $template) }}"
                                class="text-xs text-blue-600 hover:underline font-medium">Edit</a>
                            @if($template->is_active)
                            <form method="POST" action="{{ route('activity-templates.destroy', $template) }}"
                                onsubmit="return confirm('Deactivate this template? It will no longer generate daily activities.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline font-medium">Deactivate</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('activity-templates.restore', $template) }}">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:underline font-medium">Reactivate</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                        No templates yet. Create one to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($templates->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $templates->links() }}</div>
        @endif
    </div>
</div>
@endsection
