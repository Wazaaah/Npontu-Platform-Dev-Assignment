<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Operations Tracker') &mdash; Npontu Technologies</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full bg-slate-50">

<div class="flex h-full">

    {{-- ── Sidebar ──────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] flex flex-col border-r border-[#1e293b]">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 h-14 border-b border-[#1e293b] shrink-0">
            <div class="w-7 h-7 bg-indigo-600 rounded-md flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                             m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white text-sm font-semibold tracking-tight leading-none">Npontu</p>
                <p class="text-slate-500 text-[11px] mt-0.5 leading-none">Operations Tracker</p>
            </div>
        </div>

        {{-- User identity --}}
        <div class="px-3 py-2.5 border-b border-[#1e293b] shrink-0">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2.5 px-2.5 py-2 rounded-md hover:bg-white/5 transition-colors group">
                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white
                            text-xs font-bold shrink-0 group-hover:ring-2 ring-indigo-400 transition-all">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-slate-200 text-xs font-medium truncate leading-snug">{{ auth()->user()->name }}</p>
                    <p class="text-slate-500 text-[11px] leading-snug">
                        {{ ucfirst(auth()->user()->role) }}@if(auth()->user()->shift) &bull; {{ ucfirst(auth()->user()->shift) }} shift @endif
                    </p>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-600 shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3
                             m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </x-nav-link>

            <x-nav-link :href="route('activities.index')" :active="request()->routeIs('activities.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Daily Activities
            </x-nav-link>

            <x-nav-link :href="route('incidents.index')" :active="request()->routeIs('incidents.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3
                             L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Incident Reports
            </x-nav-link>

            <x-nav-link :href="route('handover.index')" :active="request()->routeIs('handover.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Shift Handover
            </x-nav-link>

            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0
                             V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0
                             V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reports
            </x-nav-link>

            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-1 px-2">
                <p class="text-[10px] font-semibold text-slate-600 uppercase tracking-widest">Administration</p>
            </div>

            <x-nav-link :href="route('activity-templates.index')" :active="request()->routeIs('activity-templates.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Activity Templates
            </x-nav-link>

            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                             M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                             m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manage Users
            </x-nav-link>

            <x-nav-link :href="route('audit-log.index')" :active="request()->routeIs('audit-log.*')">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Audit Log
            </x-nav-link>
            @endif
        </nav>

        {{-- Sign out --}}
        <div class="px-2.5 py-3 border-t border-[#1e293b] shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm text-slate-500
                           hover:text-slate-300 hover:bg-white/5 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main content ─────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-h-screen pl-64">

        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 h-14 shrink-0 sticky top-0 z-40 flex items-center px-6 justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-[15px] font-semibold text-slate-900 leading-none">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                <span class="text-slate-300 text-sm leading-none">/</span>
                <p class="text-sm text-slate-500 truncate leading-none">@yield('page-subtitle')</p>
                @endif
            </div>

            <div class="flex items-center gap-4">
                {{-- Live clock --}}
                <span id="clock"
                      class="font-mono text-xs text-slate-400 tabular-nums hidden sm:block">
                    {{ now()->format('D, d M Y · H:i') }}
                </span>

                {{-- Shift indicator --}}
                @if(auth()->user()->shift)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ auth()->user()->shift === 'morning'
                        ? 'bg-amber-50 text-amber-700 border border-amber-200'
                        : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->shift === 'morning' ? 'bg-amber-500' : 'bg-indigo-500' }}"></span>
                    {{ auth()->user()->shift === 'morning' ? 'Morning' : 'Night' }} Shift
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                             bg-purple-50 text-purple-700 border border-purple-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Manager
                </span>
                @endif

                {{-- Notification Bell --}}
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="relative p-1.5 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5
                                     a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                                     L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-red-500 text-white
                                     text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak
                         class="absolute right-0 top-full mt-1.5 w-80 bg-white border border-slate-200
                                rounded-lg shadow-lg z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-800">Notifications</p>
                            @if($unreadCount > 0)
                            <form method="POST" action="{{ route('notifications.mark-read') }}">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                    Mark all read
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse(auth()->user()->notifications->take(10) as $notification)
                            @php $d = $notification->data; @endphp
                            <div class="px-4 py-3 border-b border-slate-50 flex items-start gap-3
                                        {{ $notification->read_at ? '' : 'bg-indigo-50/40' }}">
                                <span class="mt-1 w-2 h-2 rounded-full shrink-0
                                    {{ $d['severity'] === 'critical' ? 'bg-red-500' : 'bg-orange-400' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate">{{ $d['title'] }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ ucfirst($d['severity']) }} severity &bull; {{ ucfirst($d['shift']) }} shift
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        by {{ $d['reported_by'] }} &bull;
                                        <a href="{{ route('incidents.index') }}"
                                           class="text-indigo-600 hover:underline">View</a>
                                        &bull; {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-8 text-center text-xs text-slate-400">No notifications yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-5 space-y-2">
            @if(session('success'))
            <div class="alert-success">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert-error">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
function updateClock() {
    const el = document.getElementById('clock');
    if (!el) return;
    const now = new Date();
    const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    el.textContent = `${days[now.getDay()]}, ${String(now.getDate()).padStart(2,'0')} ${months[now.getMonth()]} ${now.getFullYear()} · ${h}:${m}:${s}`;
}
updateClock();
setInterval(updateClock, 1000);
</script>
</body>
</html>
