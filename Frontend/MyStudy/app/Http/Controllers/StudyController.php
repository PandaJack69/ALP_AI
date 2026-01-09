<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SleepLog;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class StudyController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = now()->format('l'); 

        // Get class schedules for today
        $schedules = $user->classSchedules()
            ->where('day_of_week', $today)
            ->get();

        // Get last sleep log
        $lastSleep = $user->sleepLogs()->latest()->first();
        
        // Calculate sleep duration (default 8 hours if empty)
        $sleepDuration = 8;
        $stressLevel = 3; // Default stress level
        
        if ($lastSleep) {
            $start = Carbon::parse($lastSleep->sleep_at);
            $end = Carbon::parse($lastSleep->wake_at);
            $sleepDuration = $start->diffInHours($end);
            
            if ($lastSleep->quality_rating) {
                $stressLevel = max(1, min(5, 6 - $lastSleep->quality_rating));
            }
        }

        // Collect busy hours from class schedules
        $busyHours = [];
        foreach ($schedules as $schedule) {
            $startHour = intval(explode(':', $schedule->start_time)[0]);
            $endHour = intval(explode(':', $schedule->end_time)[0]);
            
            for ($i = $startHour; $i < $endHour; $i++) {
                $busyHours[] = $i;
            }
        }
        $busyHours = array_unique($busyHours);
        sort($busyHours);

        // Prepare input data
        $inputData = [
            "last_sleep_duration" => $sleepDuration,
            "busy_hours" => $busyHours,
            "current_stress_level" => $stressLevel,
            "user_bias" => (float)($user->bias_adjustment ?? 0)
        ];

        Log::info('AI Input Data prepared', $inputData);

        // Find Python script
        $scriptPath = null;
        $possiblePaths = [
            base_path('../Backend/predict.py'),
            base_path('../../Backend/predict.py'),
            __DIR__ . '/../../../../Backend/predict.py',
            realpath(base_path('../../../Backend/predict.py')),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $scriptPath = realpath($path);
                Log::info("Found Python script at: " . $scriptPath);
                break;
            }
        }

        if (!$scriptPath) {
            Log::error("Python script not found!");
            return view('dashboard', [
                'recommendations' => null,
                'today' => $today,
                'schedules' => $schedules,
                'lastSleep' => $lastSleep,
                'error' => 'AI model not found.',
                'inputData' => $inputData
            ]);
        }

        // Execute Python script
        $jsonInput = json_encode($inputData);
        Log::info("JSON input for Python: " . $jsonInput);
        
        $recommendations = null;
        $error = null;

        try {
            // Windows command with proper escaping
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $escapedJson = '"' . str_replace('"', '\\"', $jsonInput) . '"';
                $command = 'python "' . $scriptPath . '" ' . $escapedJson . ' 2>&1';
            } else {
                $escapedJson = escapeshellarg($jsonInput);
                $command = 'python3 "' . $scriptPath . '" ' . $escapedJson . ' 2>&1';
            }

            Log::info("Executing command: " . $command);
            
            set_time_limit(30);
            
            $output = shell_exec($command);
            
            if ($output === null) {
                throw new \Exception("Python script returned no output");
            }

            $output = trim($output);
            Log::info("Python output received, length: " . strlen($output));
            
            // SIMPLE DIRECT JSON PARSE
            $recommendations = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("JSON decode error: " . json_last_error_msg());
                Log::error("Output sample: " . substr($output, 0, 500));
                $error = "Failed to parse AI response";
                
                // Try to fix common JSON issues
                $output = $this->cleanJsonOutput($output);
                $recommendations = json_decode($output, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("Still failed after cleaning");
                    $recommendations = null;
                }
            }
            
            if ($recommendations && isset($recommendations['error'])) {
                Log::error("Python script error: " . $recommendations['error']);
                $error = "AI error: " . $recommendations['error'];
                $recommendations = null;
            } else if ($recommendations && !isset($recommendations['top_3_hours'])) {
                Log::error("AI response missing top_3_hours");
                $error = "AI response incomplete";
                $recommendations = null;
            } else if ($recommendations) {
                Log::info("AI prediction successful! Found " . count($recommendations['top_3_hours']) . " recommendations");
            }

        } catch (\Exception $e) {
            Log::error("Exception in AI prediction: " . $e->getMessage());
            $error = "Exception: " . $e->getMessage();
        }

        return view('dashboard', [
            'recommendations' => $recommendations,
            'today' => $today,
            'schedules' => $schedules,
            'lastSleep' => $lastSleep,
            'error' => $error,
            'inputData' => $inputData
        ]);
    }

    /**
     * Clean JSON output - remove any non-JSON content
     */
    private function cleanJsonOutput($output)
    {
        // Remove any lines that don't look like JSON
        $lines = explode("\n", $output);
        $jsonLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Keep lines that start with { or [ or contain JSON structure
            if (preg_match('/^[{\[]/', $line) || 
                preg_match('/^[^}{]*[}:][^}{]*$/', $line) ||
                strpos($line, '"') !== false) {
                $jsonLines[] = $line;
            }
        }
        
        return implode("\n", $jsonLines);
    }

    public function storeSleep(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validated = $request->validate([
            'sleep_at' => 'required|date',
            'wake_at' => 'required|date|after:sleep_at',
            'quality_rating' => 'nullable|integer|min:1|max:5'
        ]);
        
        $user->sleepLogs()->create($validated);
        
        return back()->with('success', 'Log tidur berhasil disimpan!');
    }

    public function storeSchedule(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        $user->classSchedules()->create($validated);
        
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

    /**
     * Extract JSON from Python output
     */
    /*
    private function extractJsonFromOutput($output)
    {
        // Remove the debug line if present
        $output = str_replace("DEBUG: Starting prediction\n", "", $output);
        $output = str_replace("DEBUG: Starting prediction", "", $output);
        
        // Method 1: Look for the FIRST complete JSON object (from the very beginning)
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines
            if (empty($line)) {
                continue;
            }
            
            // If line starts with {, it's likely our JSON
            if (strpos($line, '{') === 0) {
                // Count opening and closing braces to find complete JSON
                $openBraces = substr_count($line, '{');
                $closeBraces = substr_count($line, '}');
                
                // If braces are balanced, this might be the complete JSON
                if ($openBraces === $closeBraces) {
                    return $line;
                }
                
                // If not balanced, look for more lines
                $jsonLines = [$line];
                $currentOpen = $openBraces - $closeBraces;
                
                // Look at subsequent lines to complete JSON
                foreach ($lines as $nextLine) {
                    if ($nextLine === $line) continue; // Skip the first line
                    
                    $nextLine = trim($nextLine);
                    $jsonLines[] = $nextLine;
                    
                    $currentOpen += substr_count($nextLine, '{');
                    $currentOpen -= substr_count($nextLine, '}');
                    
                    // If braces are balanced, we have complete JSON
                    if ($currentOpen === 0) {
                        $jsonStr = implode("\n", $jsonLines);
                        
                        // Validate it looks like our expected JSON
                        if (strpos($jsonStr, 'top_3_hours') !== false && 
                            strpos($jsonStr, 'schedule') !== false) {
                            return $jsonStr;
                        }
                        break;
                    }
                }
            }
        }
        
        // Method 2: Simple regex to find JSON between first { and last }
        $pattern = '/^\{.*\}$/s';
        if (preg_match($pattern, $output, $matches)) {
            return $matches[0];
        }
        
        // Method 3: Try to parse line by line looking for JSON
        $jsonCandidates = [];
        $currentJson = '';
        $braceCount = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip debug lines
            if (strpos($line, 'DEBUG:') === 0) {
                continue;
            }
            
            // Count braces
            $braceCount += substr_count($line, '{');
            $braceCount -= substr_count($line, '}');
            
            $currentJson .= $line;
            
            // If braces are balanced, we might have a complete JSON
            if ($braceCount === 0 && !empty($currentJson)) {
                $jsonCandidates[] = $currentJson;
                $currentJson = '';
            }
        }
        
        // Return the longest candidate that looks like our JSON
        foreach ($jsonCandidates as $candidate) {
            if (strpos($candidate, 'top_3_hours') !== false && 
                strpos($candidate, 'schedule') !== false) {
                return $candidate;
            }
        }
        
        // Last resort: return the first line that starts with {
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '{') === 0) {
                return $line;
            }
        }
        
        return null;
    }
    */

    /**
     * Helper function to fix common JSON issues
     */
    // private function fixJson($jsonStr)
    // {
    //     // Fix 1: Remove extra spaces after { and before property names
    //     $jsonStr = preg_replace('/\{\s*"/', '{"', $jsonStr);
    //     $jsonStr = preg_replace('/,\s*"/', ',"', $jsonStr);
        
    //     // Fix 2: Ensure property names have quotes
    //     $jsonStr = preg_replace('/(\w+)\s*:/', '"$1":', $jsonStr);
        
    //     // Fix 3: Remove control characters
    //     $jsonStr = preg_replace('/[[:cntrl:]]/', '', $jsonStr);
        
    //     return $jsonStr;
    // }

    
}