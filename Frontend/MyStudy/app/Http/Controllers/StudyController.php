<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SleepLog;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class StudyController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth'); // Hanya user login yang bisa akses
    // }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil data tidur terakhir asli dari user ini
        $lastSleep = $user->sleepLogs()->latest()->first();
        $sleepDuration = 7; // Default jika belum pernah input
        if ($lastSleep) {
            $sleepDuration = Carbon::parse($lastSleep->sleep_at)->diffInHours(Carbon::parse($lastSleep->wake_at));
        }

        // Ambil jadwal kuliah hari ini
        $today = Carbon::now()->format('l');
        $schedules = $user->classSchedules()->where('day_of_week', $today)->get();

        $busyHours = [];
        foreach ($schedules as $class) {
            $start = Carbon::parse($class->start_time)->hour;
            $end = Carbon::parse($class->end_time)->hour;
            for ($i = $start; $i <= $end; $i++) { $busyHours[] = $i; }
        }

        // Jalankan AI Predictor
        $inputData = [
            "last_sleep_duration" => $sleepDuration,
            "busy_hours" => $busyHours,
            "current_stress_level" => 3 
        ];

        $jsonInput = json_encode($inputData);
        $scriptPath = base_path('../Backend/predict.py');
        $pythonPath = "python"; 
        
        // Pemanggilan shell yang aman
        $command = "{$pythonPath} \"{$scriptPath}\" " . escapeshellarg($jsonInput);
        $output = shell_exec($command);
        $recommendations = json_decode($output, true);

        return view('dashboard', compact('recommendations', 'sleepDuration', 'today'));
    }

    public function storeSleep(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->sleepLogs()->create($request->all());
        return back()->with('success', 'Log tidur berhasil disimpan!');
    }

    public function storeSchedule(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->classSchedules()->create($request->all());
        return back()->with('success', 'Jadwal kuliah berhasil disimpan!');
    }

    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $logs = $user->sleepLogs()->latest()->get();
        return view('history', compact('logs'));
    }

    public function schedules()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $schedules = $user->classSchedules()->orderBy('day_of_week')->get();
        return view('schedules', compact('schedules'));
    }
    // public function history()
    // {
    //     $logs = Auth::user()->sleepLogs()->latest()->get();
    //     return view('history', compact('logs'));
    // }

    // public function schedules()
    // {
    //     $schedules = Auth::user()->classSchedules()->orderBy('day_of_week')->get();
    //     return view('schedules', compact('schedules'));
    // }

}