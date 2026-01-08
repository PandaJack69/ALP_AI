<x-app-layout>
    <div class="flex h-screen bg-gray-900 text-gray-100 overflow-hidden">
        
        @include('layouts.sidebar')

        <main class="flex-1 p-12 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-8">Riwayat Tidur</h1>
            
            @if($logs->isEmpty())
                <div class="bg-gray-800 border-2 border-dashed border-gray-700 rounded-2xl p-20 text-center">
                    <p class="text-gray-500 italic text-lg text-center">Belum ada riwayat tidur yang tercatat. Silakan isi melalui Dashboard.</p>
                </div>
            @else
                <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-900 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="p-4">Waktu Tidur</th>
                                <th class="p-4">Waktu Bangun</th>
                                <th class="p-4">Kualitas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-750">
                                    <td class="p-4">{{ $log->sleep_at }}</td>
                                    <td class="p-4">{{ $log->wake_at }}</td>
                                    <td class="p-4 text-yellow-400 font-bold">{{ $log->quality_rating }}/5</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
</x-app-layout>