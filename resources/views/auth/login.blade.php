<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &mdash; Npontu Operations Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
</head>
<body class="h-full bg-slate-50 flex">

    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-[420px] xl:w-[480px] bg-[#0f172a] flex-col justify-between p-10 shrink-0">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-600 rounded-md flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                 m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold text-sm tracking-tight">Npontu Technologies</span>
            </div>
        </div>

        <div>
            <h2 class="text-white text-2xl font-bold leading-tight mb-3">
                Applications Support<br>Operations Tracker
            </h2>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">
                Real-time shift activity tracking, incident management, and handover reporting for your support team.
            </p>

            <div class="space-y-3">
                @foreach([
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Track daily activities across morning & night shifts'],
                    ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'label' => 'Log and resolve incidents with full audit trail'],
                    ['icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'label' => 'Structured shift handover with pending item visibility'],
                ] as $feat)
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">{{ $feat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-slate-600 text-xs">&copy; {{ date('Y') }} Npontu Technologies. Internal use only.</p>
    </div>

    {{-- Right panel — login form --}}
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-sm">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-2.5 mb-8 lg:hidden">
                <div class="w-8 h-8 bg-indigo-600 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                 m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-slate-900 font-semibold text-sm">Npontu Technologies</span>
            </div>

            <h1 class="text-xl font-bold text-slate-900 mb-1">Welcome back</h1>
            <p class="text-sm text-slate-500 mb-7">Sign in to your account to continue</p>

            @if($errors->any())
            <div class="flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-lg mb-5">
                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-red-700">{{ $errors->first() }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block w-full rounded-md border-slate-300 bg-white text-slate-900
                               placeholder-slate-400 shadow-none focus:border-indigo-500 focus:ring-1
                               focus:ring-indigo-500 sm:text-sm"
                        placeholder="you@npontu.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">
                        Password
                    </label>
                    <input type="password" name="password" required
                        class="block w-full rounded-md border-slate-300 bg-white text-slate-900
                               placeholder-slate-400 shadow-none focus:border-indigo-500 focus:ring-1
                               focus:ring-indigo-500 sm:text-sm"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-md
                           hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2
                           focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Sign In
                </button>
            </form>

            <div class="mt-6 p-3 bg-slate-100 rounded-md border border-slate-200">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Demo Credentials</p>
                <div class="space-y-1 font-mono text-xs text-slate-600">
                    <p>admin@npontu.com &mdash; password</p>
                    <p>kwame@npontu.com &mdash; password</p>
                    <p>ama@npontu.com &mdash; password</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
