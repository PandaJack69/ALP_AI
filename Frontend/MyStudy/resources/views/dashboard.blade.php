<!DOCTYPE html>
<html>
<head>
    <title>Smart Study Time</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6 text-blue-600">Smart Study Time Recommender</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h3 class="font-bold mb-4 text-blue-800">Log Tidur Terakhir</h3>
                <form action="{{ route('sleep.store') }}" method="POST">
                    @csrf
                    <label class="block text-sm">Jam Tidur:</label>
                    <input type="datetime-local" name="sleep_at" required class="w-full mb-2 p-2 rounded border">
                    <label class="block text-sm">Jam Bangun:</label>
                    <input type="datetime-local" name="wake_at" required class="w-full mb-2 p-2 rounded border">
                    <label class="block text-sm">Kualitas (1-5):</label>
                    <input type="number" name="quality_rating" min="1" max="5" required class="w-full mb-4 p-2 rounded border">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full hover:bg-blue-700 transition">Simpan Log Tidur</button>
                </form>
            </div>

            <div class="bg-purple-50 p-6 rounded-lg shadow-sm">
                <h3 class="font-bold mb-4 text-purple-800">Tambah Jadwal Kuliah</h3>
                <form action="{{ route('schedule.store') }}" method="POST">
                    @csrf
                    <input type="text" name="subject_name" placeholder="Nama Mata Kuliah" required class="w-full mb-2 p-2 rounded border">
                    <select name="day_of_week" class="w-full mb-2 p-2 rounded border">
                        <option value="Monday">Senin</option>
                        <option value="Tuesday">Selasa</option>
                        <option value="Wednesday">Rabu</option>
                        <option value="Thursday">Kamis</option>
                        <option value="Friday">Jumat</option>
                        <option value="Saturday">Sabtu</option>
                        <option value="Sunday">Minggu</option>
                    </select>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs">Mulai:</label>
                            <input type="time" name="start_time" required class="w-full p-2 rounded border">
                        </div>
                        <div>
                            <label class="text-xs">Selesai:</label>
                            <input type="time" name="end_time" required class="w-full p-2 rounded border">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded w-full hover:bg-purple-700 transition">Tambah Jadwal</button>
                </form>
            </div>
        </div>

        <hr class="mb-10">

        @if(isset($recommendations['error']))
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
                {{ $recommendations['error'] }}
            </div>

        @else
            @if(isset($recommendations['top_3_hours']) && is_array($recommendations['top_3_hours']))
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Top 3 Waktu Belajar Terbaik Hari Ini</h2>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($recommendations['top_3_hours'] as $top)
                            <div class="bg-green-100 p-4 rounded-lg border-l-4 border-green-500">
                                <p class="text-sm text-gray-600">Pukul</p>
                                <p class="text-2xl font-bold">{{ $top['formatted_time'] }}</p>
                                <p class="text-sm font-semibold text-green-700">Skor: {{ $top['score'] * 100 }}%</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-yellow-100 p-4 rounded mb-8 text-yellow-700">
                    Belum ada rekomendasi. Silakan isi Log Tidur dan Jadwal Kuliah terlebih dahulu.
                </div>
            @endif
            
            @endif
        {{-- @else
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Top 3 Waktu Belajar Terbaik Hari Ini</h2>
                <div class="grid grid-cols-3 gap-4">
                    @foreach($recommendations['top_3_hours'] as $top)
                        <div class="bg-green-100 p-4 rounded-lg border-l-4 border-green-500">
                            <p class="text-sm text-gray-600">Pukul</p>
                            <p class="text-2xl font-bold">{{ $top['formatted_time'] }}</p>
                            <p class="text-sm font-semibold text-green-700">Skor: {{ $top['score'] * 100 }}%</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Prediksi Produktivitas 24 Jam</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-2 border">Jam</th>
                                <th class="p-2 border">Skor Prediksi AI</th>
                                <th class="p-2 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations['schedule'] as $item)
                                <tr class="{{ $item['status'] != 'Available' ? 'bg-gray-50 text-gray-400' : '' }}">
                                    <td class="p-2 border">{{ $item['formatted_time'] }}</td>
                                    <td class="p-2 border">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $item['score'] * 100 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="p-2 border">
                                        <span class="px-2 py-1 rounded text-xs {{ $item['status'] == 'Available' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}
    </div>
</body>
</html>