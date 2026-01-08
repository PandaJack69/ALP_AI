<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyController; // Semua import diletakkan di atas
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman depan (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Grup Route yang Membutuhkan Login (Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama (Hasil AI)
    Route::get('/dashboard', [StudyController::class, 'index'])->name('dashboard');
    
    // Fitur Simpan Data (Data Acquisition)
    Route::post('/sleep-store', [StudyController::class, 'storeSleep'])->name('sleep.store');
    Route::post('/schedule-store', [StudyController::class, 'storeSchedule'])->name('schedule.store');

    // Route Profil (Bawaan Breeze - Sebaiknya jangan dihapus agar user bisa logout/edit profil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tambahkan ini:
    Route::get('/history', [StudyController::class, 'history'])->name('history.index');
    Route::get('/schedules', [StudyController::class, 'schedules'])->name('schedules.index');
});

// Add this route for feedback
Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

Route::get('/test-ai', function() {
    $testData = [
        "last_sleep_duration" => 5,
        "busy_hours" => [12,13,14],
        "current_stress_level" => 3,
        "user_bias" => 0
    ];
    
    $scriptPath = base_path('../../Backend/predict.py');
    
    if (!file_exists($scriptPath)) {
        return "Script not found at: " . $scriptPath;
    }
    
    $jsonInput = json_encode($testData);
    $escapedJson = '"' . str_replace('"', '\\"', $jsonInput) . '"';
    $command = 'python "' . $scriptPath . '" ' . $escapedJson . ' 2>&1';
    
    echo "<h3>Command:</h3>";
    echo "<pre>" . htmlspecialchars($command) . "</pre>";
    
    $output = shell_exec($command);
    $output = trim($output);
    
    echo "<h3>Raw Output:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    echo "<h3>Parsing Result:</h3>";
    
    // Direct JSON decode
    $data = json_decode($output, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<div style='color: green; font-weight: bold;'>✓ JSON Parsed Successfully!</div>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
        if (isset($data['top_3_hours'])) {
            echo "<div style='color: green;'>Found " . count($data['top_3_hours']) . " recommendations!</div>";
        }
    } else {
        echo "<div style='color: red; font-weight: bold;'>✗ JSON Parse Error: " . json_last_error_msg() . "</div>";
        
        // Show what we got
        echo "<h4>Output analysis:</h4>";
        echo "Length: " . strlen($output) . "<br>";
        echo "First 100 chars: " . htmlspecialchars(substr($output, 0, 100)) . "<br>";
        echo "Last 100 chars: " . htmlspecialchars(substr($output, -100)) . "<br>";
        
        // Try to find JSON manually
        $firstBrace = strpos($output, '{');
        $lastBrace = strrpos($output, '}');
        
        if ($firstBrace !== false && $lastBrace !== false) {
            echo "<h4>Attempting to extract JSON from position $firstBrace to $lastBrace:</h4>";
            $extracted = substr($output, $firstBrace, $lastBrace - $firstBrace + 1);
            echo "<pre>" . htmlspecialchars($extracted) . "</pre>";
            
            $data2 = json_decode($extracted, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<div style='color: green;'>✓ Extracted JSON works!</div>";
            }
        }
    }
});

require __DIR__.'/auth.php';