<aside class="w-64 bg-gray-800 border-r border-gray-700 hidden md:block">
    <div class="p-6">
        <h2 class="text-xl font-bold text-indigo-400 tracking-widest uppercase">
            MyStudy AI
        </h2>
    </div>

    <nav class="mt-4 px-4 space-y-2">
        <a href="{{ route('dashboard') }}"
           class="flex items-center p-3 rounded-lg transition
           {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Dashboard AI
        </a>

        <a href="{{ route('history.index') }}"
           class="flex items-center p-3 rounded-lg transition
           {{ request()->routeIs('history.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Log History
        </a>

        <a href="{{ route('schedules.index') }}"
           class="flex items-center p-3 rounded-lg transition
           {{ request()->routeIs('schedules.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Jadwal Kuliah
        </a>
    </nav>
</aside>
