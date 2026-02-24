@extends('layouts.app')
@section('title', 'Daily Activities')
@section('page-title', 'Daily Activities')
@section('page-subtitle', 'View and update activities for your shift')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="card px-4 py-3">
        <form method="GET" action="{{ route('activities.index') }}" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="form-label">Date</label>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    class="form-input" onchange="this.form.submit()">
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="form-label">Shift</label>
                <select name="shift" class="form-input" onchange="this.form.submit()">
                    <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>Morning</option>
                    <option value="night"   {{ $shift === 'night'   ? 'selected' : '' }}>Night</option>
                </select>
            </div>
            @else
            <div class="flex items-end pb-0.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $shift === 'morning'
                        ? 'bg-amber-50 text-amber-700 border border-amber-200'
                        : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $shift === 'morning' ? 'bg-amber-400' : 'bg-indigo-400' }}"></span>
                    {{ ucfirst($shift) }} Shift
                </span>
            </div>
            @endif

            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Filter by name&hellip;" class="form-input w-52">
            </div>

            <button type="submit" class="btn-secondary">Search</button>

            @if($search)
            <a href="{{ route('activities.index', ['date' => $date->format('Y-m-d'), 'shift' => $shift]) }}"
               class="btn-secondary">Clear</a>
            @endif

            <div class="flex items-end pb-0.5 text-sm text-slate-500 ml-auto">
                <span class="font-semibold text-slate-700">{{ $activities->count() }}</span>
                <span class="ml-1">activities &bull; <span class="font-mono">{{ $date->format('d M Y') }}</span></span>
            </div>
        </form>
    </div>

    {{-- Activities table --}}
    <div class="card overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-6"></th>
                    <th>Activity</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Last Updated By</th>
                    <th>Remark</th>
                    @if(!auth()->user()->isAdmin())
                    <th class="text-right">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($activities as $activity)

                {{-- Activity row --}}
                <tr>
                    <td class="pl-4 pr-0">
                        <span class="block w-1.5 h-1.5 rounded-full {{ $activity->status === 'done' ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                    </td>
                    <td class="pl-2">
                        <p class="font-medium text-slate-800">{{ $activity->template->name }}</p>
                        @if($activity->template->description)
                        <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">{{ $activity->template->description }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                     bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $activity->template->category_label }}
                        </span>
                    </td>
                    <td>
                        <span class="{{ $activity->status === 'done' ? 'badge-done' : 'badge-pending' }}">
                            {{ ucfirst($activity->status) }}
                        </span>
                    </td>
                    <td>
                        @if($activity->latestUpdate)
                        <p class="text-sm text-slate-700 font-medium">{{ $activity->latestUpdate->updatedBy->name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $activity->latestUpdate->updated_at_time->format('H:i, d M') }}</p>
                        @else
                        <span class="text-xs text-slate-300">Not updated yet</span>
                        @endif
                    </td>
                    <td class="max-w-xs">
                        @if($activity->latestUpdate && $activity->latestUpdate->remark)
                        <p class="text-xs text-slate-600 truncate">&ldquo;{{ Str::limit($activity->latestUpdate->remark, 55) }}&rdquo;</p>
                        @else
                        <span class="text-xs text-slate-300">&mdash;</span>
                        @endif
                    </td>
                    @if(!auth()->user()->isAdmin())
                    <td class="text-right pr-4">
                        <button onclick="openModal('modal-{{ $activity->id }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold
                                   text-indigo-700 bg-indigo-50 border border-indigo-200
                                   hover:bg-indigo-100 transition-colors">
                            Update
                        </button>
                    </td>
                    @endif
                </tr>

                {{-- History row --}}
                @if($activity->updates->count() > 0)
                <tr class="bg-slate-50/60">
                    <td colspan="{{ auth()->user()->isAdmin() ? 6 : 7 }}" class="px-4 py-2">
                        <details class="text-xs">
                            <summary class="text-indigo-600 cursor-pointer font-semibold select-none inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Update history &mdash; {{ $activity->updates->count() }} {{ Str::plural('entry', $activity->updates->count()) }}
                            </summary>
                            <div class="mt-2.5 ml-4 space-y-2 pb-1">
                                @foreach($activity->updates as $upd)
                                <div class="flex items-start gap-3 pl-3 border-l-2 border-indigo-200">
                                    <span class="font-semibold shrink-0 {{ $upd->status === 'done' ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ ucfirst($upd->status) }}
                                    </span>
                                    <span class="text-slate-600">by <strong class="text-slate-800">{{ $upd->updatedBy->name }}</strong></span>
                                    <span class="text-slate-400 font-mono">{{ $upd->updated_at_time->format('H:i, d M Y') }}</span>
                                    @if($upd->remark)
                                    <span class="italic text-slate-500">&ldquo;{{ $upd->remark }}&rdquo;</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </details>
                    </td>
                </tr>
                @endif

                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? 6 : 7 }}" class="px-4 py-14 text-center">
                        <p class="text-sm text-slate-400">No activities found for this date and shift.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Update modals --}}
@if(!auth()->user()->isAdmin())
@foreach($activities as $activity)
<div id="modal-{{ $activity->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Update Activity</h3>
            <button onclick="closeModal('modal-{{ $activity->id }}')"
                class="w-7 h-7 flex items-center justify-center rounded-md text-slate-400
                       hover:text-slate-600 hover:bg-slate-100 transition-colors text-lg leading-none">
                &times;
            </button>
        </div>
        <form method="POST" action="{{ route('activities.update', $activity) }}">
            @csrf @method('PATCH')
            <div class="px-6 py-5 space-y-4">
                <div class="bg-slate-50 border border-slate-100 rounded-md px-3.5 py-2.5">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Activity</p>
                    <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $activity->template->name }}</p>
                </div>
                <div>
                    <label class="form-label">Status <span class="text-red-400 normal-case tracking-normal">*</span></label>
                    <select name="status" class="form-input" required>
                        <option value="pending" {{ $activity->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="done"    {{ $activity->status === 'done'    ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Remark</label>
                    <textarea name="remark" rows="3" class="form-input resize-none"
                        placeholder="Add a note&hellip;"></textarea>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-slate-50 border border-slate-100 rounded-md px-3 py-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Logged as <strong class="text-slate-600">{{ auth()->user()->name }}</strong>
                    &bull; <span class="font-mono">{{ now()->format('H:i, d M Y') }}</span>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-{{ $activity->id }}')" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">Save Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach
<script>
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
@endif
@endsection
