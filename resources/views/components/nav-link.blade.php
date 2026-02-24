@props(['href', 'active' => false])
<a href="{{ $href }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
          {{ $active
              ? 'bg-indigo-600/20 text-indigo-300 ring-1 ring-inset ring-indigo-500/30'
              : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
    {{ $slot }}
</a>
