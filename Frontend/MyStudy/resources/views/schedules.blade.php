<x-app-layout>
    <div class="flex h-screen bg-gray-900 text-gray-100 overflow-hidden">
        
        @include('layouts.sidebar')

        <main class="flex-1 overflow-y-auto p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-white">Master Jadwal Kuliah</h1>
                <p class="text-gray-400 mt-1">Daftar semua mata kuliah yang membatasi waktu belajar AI kamu.</p>
            </div>

            @if($schedules->isEmpty())
                <div class="bg-gray-800 border-2 border-dashed border-gray-700 rounded-3xl p-20 flex flex-col items-center justify-center text-center">
                    <div class="bg-gray-700 p-4 rounded-full mb-4">
                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168 0.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332 0.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332 0.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wider">Belum Ada Jadwal</h3>
                    <p class="text-gray-500 max-w-sm mt-2">Database jadwal kamu masih kosong. Silakan tambahkan jadwal kuliah melalui Dashboard AI.</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 px-6 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg font-bold transition">Kembali ke Dashboard</a>
                </div>
            @else
                <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-900 border-b border-gray-700">
                            <tr>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest">Mata Kuliah</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest">Hari</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest text-center">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($schedules as $schedule)
                                <tr class="hover:bg-gray-700/50 transition duration-150">
                                    <td class="p-4">
                                        <div class="font-bold text-white">{{ $schedule->subject_name }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 bg-purple-900/30 text-purple-400 text-[10px] font-black uppercase rounded-full border border-purple-500/20">
                                            {{ $schedule->day_of_week }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-mono text-indigo-300">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
</x-app-layout>