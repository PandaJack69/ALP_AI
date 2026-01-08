<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SleepLog;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudyController extends Controller
{
    public function index()
    {
        // $userId = auth()->id() ?? 1;
        $userId = Auth::id() ?? 1; // Pakai ID 1 untuk testing jika belum ada Auth

        // 1. Ambil data tidur terakhir untuk hitung durasi (Sleep Quality Score) [cite: 98, 100]
        $lastSleep = SleepLog::where('user_id', $userId)->latest()->first();
        $sleepDuration = 7; // default
        if ($lastSleep) {
            $sleepDuration = Carbon::parse($lastSleep->sleep_at)->diffInHours(Carbon::parse($lastSleep->wake_at));
        }

        // 2. Ambil jadwal kuliah hari ini (Class Occupancy Map) [cite: 107, 108]
        $today = Carbon::now()->format('l'); // Misal: "Monday"
        $schedules = ClassSchedule::where('user_id', $userId)
                        ->where('day_of_week', $today)
                        ->get();

        $busyHours = [];
        foreach ($schedules as $class) {
            $start = Carbon::parse($class->start_time)->hour;
            $end = Carbon::parse($class->end_time)->hour;
            for ($i = $start; $i < $end; $i++) {
                $busyHours[] = $i;
            }
        }

        // 3. Siapkan JSON untuk Python [cite: 123]
        $inputData = [
            "last_sleep_duration" => $sleepDuration,
            "busy_hours" => $busyHours,
            "current_stress_level" => 3 // Bisa diambil dari input user nanti
        ];

        // 4. Panggil Script Python
        $jsonInput = json_encode($inputData);
        $scriptPath = base_path('../Backend/predict.py');
        $pythonPath = "python"; // Sesuaikan jika di Windows biasanya 'python'
        
        // Gunakan escapeshellarg untuk keamanan
        $command = "{$pythonPath} \"{$scriptPath}\" " . escapeshellarg($jsonInput);
        $output = shell_exec($command);
        $recommendations = json_decode($output, true);

        return view('dashboard', compact('recommendations', 'sleepDuration', 'today'));
    }

    public function storeSleep(Request $request) {
        SleepLog::create([
            'user_id' => Auth::id() ?? 1,
            'sleep_at' => $request->sleep_at,
            'wake_at' => $request->wake_at,
            'quality_rating' => $request->quality_rating,
        ]);
        return back()->with('success', 'Data tidur berhasil disimpan!');
    }

    public function storeSchedule(Request $request) {
        ClassSchedule::create([
            'user_id' => Auth::id() ?? 1,
            'subject_name' => $request->subject_name,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
        return back()->with('success', 'Jadwal kuliah berhasil ditambahkan!');
    }
}