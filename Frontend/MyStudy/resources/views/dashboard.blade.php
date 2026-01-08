<x-app-layout>
    <div class="flex h-screen bg-gray-900 text-gray-100 overflow-hidden">
        
        @include('layouts.sidebar')

        <main class="flex-1 overflow-y-auto p-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-white">AI Productivity Recommender</h1>
                    <p class="text-gray-400 mt-1">Berdasarkan data tidur dan jadwal hari ini.</p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500 block uppercase tracking-tighter">Hari Ini</span>
                    <span class="text-lg font-bold text-indigo-400">{{ $today }}</span>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-900/30 border border-green-500 text-green-400 p-4 rounded-lg mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293l-4 4a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414L9 10.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-xl">
                        <h3 class="text-lg font-bold text-blue-400 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            Input Log Tidur
                        </h3>
                        <form action="{{ route('sleep.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-semibold">Tidur & Bangun</label>
                                <input type="datetime-local" name="sleep_at" required class="w-full mt-1 bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300 focus:ring-blue-500">
                                <input type="datetime-local" name="wake_at" required class="w-full mt-2 bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-semibold">Kualitas (1-5)</label>
                                <input type="range" name="quality_rating" min="1" max="5" class="w-full mt-2 accent-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-lg transition duration-200">Update Data</button>
                        </form>
                    </div>

                    <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-xl">
                        <h3 class="text-lg font-bold text-purple-400 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168 0.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332 0.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332 0.477-4.5 1.253"/></svg>
                            Tambah Jadwal
                        </h3>
                        <form action="{{ route('schedule.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="text" name="subject_name" placeholder="Mata Kuliah" required class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300">
                            <select name="day_of_week" class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300">
                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="time" name="start_time" required class="bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300 text-center">
                                <input type="time" name="end_time" required class="bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-300 text-center">
                            </div>
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-2 rounded-lg transition duration-200">Simpan Jadwal</button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">
                    
                    @if(!isset($recommendations['top_3_hours']))
                        <div class="bg-gray-800 p-12 rounded-3xl border-2 border-dashed border-gray-700 flex flex-col items-center justify-center text-center">
                            <div class="bg-gray-700 p-4 rounded-full mb-4">
                                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Menunggu Data...</h3>
                            <p class="text-gray-500 max-w-xs">AI butuh log tidur dan jadwalmu untuk mulai menganalisis waktu terbaik.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($recommendations['top_3_hours'] as $index => $top)
                                <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
                                    <div class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[10px] px-3 py-1 rotate-12 font-black uppercase">Top {{ $index + 1 }}</div>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">Waktu Terbaik</p>
                                    <h4 class="text-4xl font-black text-white mt-2">{{ $top['formatted_time'] }}</h4>
                                    <div class="mt-4 h-1 w-full bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: {{ $top['score'] * 100 }}%"></div>
                                    </div>
                                    <p class="mt-2 text-green-400 font-bold text-sm italic">Skor AI: {{ round($top['score'] * 100) }}%</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- <div class="bg-gray-800 rounded-3xl border border-gray-700 shadow-2xl overflow-hidden">
                            <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg">Prediksi Produktivitas 24 Jam</h3>
                                <span class="px-3 py-1 bg-green-900/50 text-green-400 text-xs rounded-full font-bold">Live AI Engine</span>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-900 sticky top-0">
                                        <tr>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jam</th>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Skor AI</th>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700">
                                        @foreach($recommendations['schedule'] as $item)
                                            <tr class="hover:bg-gray-700/50 transition duration-150">
                                                <td class="p-4 font-mono font-bold text-indigo-300">{{ $item['formatted_time'] }}</td>
                                                <td class="p-4">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-1 h-2 bg-gray-900 rounded-full overflow-hidden">
                                                            <div class="h-full bg-indigo-500 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $item['score'] * 100 }}%"></div>
                                                        </div>
                                                        <span class="text-xs font-bold text-gray-400 w-8">{{ round($item['score'] * 100) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="p-4 text-right text-xs">
                                                    @if($item['status'] == 'Available')
                                                        <span class="text-green-500 font-black tracking-tighter uppercase px-2 py-1 bg-green-500/10 rounded">Bisa Belajar</span>
                                                    @else
                                                        <span class="text-red-400 font-black tracking-tighter uppercase px-2 py-1 bg-red-500/10 rounded">Sibuk Kuliah</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                        <div class="bg-gray-800 rounded-3xl border border-gray-700 shadow-2xl overflow-hidden">
                            <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-lg">Prediksi Produktivitas 24 Jam</h3>
                                    <p class="text-xs text-gray-500">Berikan feedback pada jam yang sudah lewat untuk melatih AI personalmu.</p>
                                </div>
                                <span class="px-3 py-1 bg-green-900/50 text-green-400 text-xs rounded-full font-bold">Hybrid AI Active</span>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-900 sticky top-0">
                                        <tr>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Jam</th>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Skor AI</th>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Feedback</th>
                                            <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700">
                                        @foreach($recommendations['schedule'] as $item)
                                            <tr class="hover:bg-gray-700/50 transition duration-150">
                                                <td class="p-4 font-mono font-bold text-indigo-300">{{ $item['formatted_time'] }}</td>
                                                <td class="p-4">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-1 h-2 bg-gray-900 rounded-full overflow-hidden">
                                                            <div class="h-full bg-indigo-500 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $item['score'] * 100 }}%"></div>
                                                        </div>
                                                        <span class="text-xs font-bold text-gray-400 w-8">{{ round($item['score'] * 100) }}%</span>
                                                    </div>
                                                </td>
                                                
                                                <td class="p-4">
                                                    <form action="{{ route('feedback.store') }}" method="POST" class="flex items-center justify-center space-x-2">
                                                        @csrf
                                                        <input type="hidden" name="ai_score" value="{{ $item['score'] }}">
                                                        <input type="hidden" name="hour" value="{{ $item['hour'] }}">
                                                        
                                                        <select name="rating" class="text-[10px] bg-gray-900 border-gray-700 rounded text-gray-300 py-1 px-2 focus:ring-indigo-500">
                                                            <option value="5">🔥 High</option>
                                                            <option value="3" selected>Neutral</option>
                                                            <option value="1">😴 Low</option>
                                                        </select>

                                                        <select name="focus_status" class="text-[10px] bg-gray-900 border-gray-700 rounded text-gray-300 py-1 px-2 focus:ring-indigo-500">
                                                            <option value="focused">Focused</option>
                                                            <option value="distracted">Distracted</option>
                                                        </select>

                                                        <button type="submit" class="p-1.5 bg-indigo-600 rounded hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">
                                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        </button>
                                                    </form>
                                                </td>

                                                <td class="p-4 text-right text-xs">
                                                    @if($item['status'] == 'Available')
                                                        <span class="text-green-500 font-black tracking-tighter uppercase px-2 py-1 bg-green-500/10 rounded border border-green-500/20">Bisa Belajar</span>
                                                    @else
                                                        <span class="text-red-400 font-black tracking-tighter uppercase px-2 py-1 bg-red-500/10 rounded border border-red-500/20">Sibuk Kuliah</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </main>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6366f1; }
    </style>
</x-app-layout>