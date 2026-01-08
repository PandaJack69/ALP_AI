import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
import joblib

# --- 1. GENERATE DUMMY DATA ---
# Karena belum ada real user, kita buat simulasi data kebiasaan belajar mahasiswa
# Kita akan buat 1000 baris data historis
np.random.seed(42)
n_samples = 1000

data = {
    'hour_of_day': np.random.randint(0, 24, n_samples), # Jam 0-23
    'sleep_duration': np.random.uniform(4, 10, n_samples), # Tidur antara 4-10 jam
    'is_weekend': np.random.choice([0, 1], n_samples), # 0: Weekday, 1: Weekend
    'stress_level': np.random.randint(1, 6, n_samples), # 1 (Rileks) - 5 (Stres)
}

df = pd.DataFrame(data)

# Fungsi untuk mensimulasikan "Target Productivity Score" (0.0 - 1.0)
# Ini meniru "Circadian Rhythm" [cite: 106] dan logika umum
def calculate_dummy_productivity(row):
    score = 0.5 # Base score
    
    # Pola jam (Morning person simulation)
    if 8 <= row['hour_of_day'] <= 11:
        score += 0.3 # Pagi sangat produktif
    elif 13 <= row['hour_of_day'] <= 15:
        score -= 0.2 # "Afternoon slump" / ngantuk siang
    elif 19 <= row['hour_of_day'] <= 21:
        score += 0.2 # Belajar malam
    elif row['hour_of_day'] >= 23 or row['hour_of_day'] <= 4:
        score -= 0.3 # Terlalu larut malam
        
    # Pengaruh tidur [cite: 98]
    if row['sleep_duration'] > 7:
        score += 0.1
    elif row['sleep_duration'] < 5:
        score -= 0.2
        
    # Stress
    score -= (row['stress_level'] * 0.05)
    
    # Clip agar tetap di range 0-1
    return max(0.0, min(1.0, score))

df['productivity_score'] = df.apply(calculate_dummy_productivity, axis=1)

# --- 2. TRAINING MODEL ---
# Fitur (X) dan Target (y)
X = df[['hour_of_day', 'sleep_duration', 'is_weekend', 'stress_level']]
y = df['productivity_score']

# Menggunakan Random Forest sesuai proposal [cite: 113]
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X, y)

# --- 3. SAVE MODEL ---
# Simpan model agar bisa dipanggil oleh script lain (Predictor)
joblib.dump(model, 'study_model.pkl')
print("Model berhasil dilatih dan disimpan sebagai 'study_model.pkl'")
print("Contoh Data Latih:")
print(df.head())