import sys
import json
import joblib
import pandas as pd
import numpy as np
from datetime import datetime

# Load Model yang sudah dilatih
try:
    model = joblib.load('study_model.pkl')
except:
    print(json.dumps({"error": "Model not found. Run train_model.py first."}))
    sys.exit(1)

def get_recommendations(user_data):
    """
    Fungsi utama untuk memprediksi 24 jam ke depan
    """
    recommendations = []
    
    # Ambil data dari input user
    sleep_duration = user_data.get('last_sleep_duration', 7)
    stress_level = user_data.get('current_stress_level', 3)
    is_weekend = 1 if datetime.today().weekday() >= 5 else 0
    class_schedule = user_data.get('busy_hours', []) # List jam sibuk, misal [9, 10, 11]

    # Loop untuk 24 jam (misal dari jam 6 pagi sampai jam 5 pagi besok)
    # Tapi agar simpel, kita prediksi jam 00 - 23 hari ini
    for hour in range(24):
        
        # --- 1. Machine Learning Prediction Layer ---
        # Siapkan fitur untuk prediksi
        features = pd.DataFrame([{
            'hour_of_day': hour,
            'sleep_duration': sleep_duration,
            'is_weekend': is_weekend,
            'stress_level': stress_level
        }])
        
        # Prediksi skor murni dari AI
        predicted_score = model.predict(features)[0]
        
        # --- 2. Rule-Based Layer (Hybrid AI)  ---
        final_score = predicted_score
        status = "Available"
        
        # Aturan A: Jangan rekomendasikan saat ada kelas [cite: 133]
        if hour in class_schedule:
            final_score = 0
            status = "Class/Busy"
            
        # Aturan B: Hindari jam tidur normal (misal jam 00-05) jika tidak terpaksa
        if 0 <= hour <= 4:
            final_score = final_score * 0.5 # Penalty untuk begadang
            status = "Late Night"
            
        recommendations.append({
            "hour": hour,
            "score": round(final_score, 2), # Skor 0.0 - 1.0
            "status": status,
            "formatted_time": f"{hour:02d}:00"
        })

    # Urutkan berdasarkan skor tertinggi (Top recommendations)
    # Ini untuk memudahkan Laravel mengambil "Top 3" [cite: 67]
    recommendations_sorted = sorted(recommendations, key=lambda x: x['score'], reverse=True)
    
    return {
        "schedule": recommendations,
        "top_3_hours": recommendations_sorted[:3]
    }

if __name__ == "__main__":
    # --- INTERFACE DENGAN LARAVEL ---
    # Laravel akan mengirim data via command line argument sebagai string JSON
    # Contoh format input: '{"last_sleep_duration": 6, "busy_hours": [8, 9, 10, 13, 14]}'
    
    try:
        if len(sys.argv) > 1:
            input_json = sys.argv[1]
            user_data = json.loads(input_json)
        else:
            # Data default untuk testing manual tanpa Laravel
            user_data = {
                "last_sleep_duration": 7, 
                "busy_hours": [9, 10, 11], # Ada kuliah jam 9-11
                "current_stress_level": 2
            }
            
        result = get_recommendations(user_data)
        
        # Print JSON ke stdout (ini yang akan ditangkap Laravel)
        print(json.dumps(result))
        
    except Exception as e:
        print(json.dumps({"error": str(e)}))