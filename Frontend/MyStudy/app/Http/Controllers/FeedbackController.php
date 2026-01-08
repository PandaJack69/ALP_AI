<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // 1. Ambil input dari user
        $userRating = $request->input('rating'); // Skala 1-5
        $aiPrediction = $request->input('ai_score'); // Skala 0.0 - 1.0 (dikirim via hidden input)
        $isFocused = $request->input('focus_status') === 'focused';

        // 2. Normalisasi Rating User ke skala 0 - 1
        // (1 -> 0.0, 3 -> 0.5, 5 -> 1.0)
        $normalizedRating = ($userRating - 1) / 4;

        // 3. Hitung Selisih (Error)
        $error = $normalizedRating - $aiPrediction;

        // 4. Update Bias dengan Learning Rate (0.1)
        // Agar perubahan tidak drastis, kita hanya ambil 10% dari error
        $learningRate = 0.1;
        $newBias = $user->bias_adjustment + ($error * $learningRate);

        // Tambahan: Penyesuaian ekstra jika statusnya 'Distracted'
        if (!$isFocused) {
            $newBias -= 0.05; 
        }

        // 5. Simpan ke Database
        $user->update([
            'bias_adjustment' => max(-0.5, min(0.5, $newBias)) // Batasi bias max +- 0.5
        ]);

        return back()->with('success', 'AI telah mempelajari preferensi barumu!');
    }
}
