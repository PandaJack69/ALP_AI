import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
import joblib
import os

def train():
    # Load Dataset Kaggle
    base_path = os.path.dirname(__file__)
    csv_path = os.path.join(base_path, 'data/Sleep_health_and_lifestyle_dataset.csv')
    
    if not os.path.exists(csv_path):
        print(f"Error: File {csv_path} tidak ditemukan!")
        return

    df = pd.read_csv(csv_path)

    # 2. Feature Engineering (Membuat Target Produktivitas)
    # Kita buat target 'productivity_score' berdasarkan kualitas tidur dan stress level
    # Formula: (Quality * 2) - Stress, lalu normalisasi 0-1
    df['productivity_score'] = (df['Quality of Sleep'] * 2) - df['Stress Level']
    df['productivity_score'] = (df['productivity_score'] - df['productivity_score'].min()) / \
                               (df['productivity_score'].max() - df['productivity_score'].min())

    # 3. Pilih Fitur
    # Kita gunakan fitur yang bisa kita dapatkan dari input user di Laravel
    features = ['Sleep Duration', 'Quality of Sleep', 'Stress Level']
    X = df[features]
    y = df['productivity_score']

    # 4. Training
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    model = RandomForestRegressor(n_estimators=100, random_state=42)
    model.fit(X_train, y_train)

    # 5. Save Model
    model_path = os.path.join(base_path, 'study_model.pkl')
    joblib.dump(model, model_path)
    
    print(f"Model trained successfully menggunakan Dataset Kaggle!")
    print(f"Accuracy (R2 Score): {model.score(X_test, y_test):.2f}")
    print(f"Model disimpan di: {model_path}")

if __name__ == "__main__":
    train()