import sys
import json
import joblib
import os
import numpy as np
import warnings

def predict():
    try:
        # Suppress ALL warnings
        warnings.filterwarnings('ignore')
        
        # 1. Handle input
        if len(sys.argv) < 2:
            return json.dumps({"error": "No input data provided"})
        
        input_str = ' '.join(sys.argv[1:])
        
        # Try parsing as JSON
        try:
            input_data = json.loads(input_str)
        except json.JSONDecodeError:
            # Clean the string
            input_str = input_str.strip()
            if (input_str.startswith('"') and input_str.endswith('"')) or \
               (input_str.startswith("'") and input_str.endswith("'")):
                input_str = input_str[1:-1]
            input_str = input_str.replace('\\"', '"').replace("\\'", "'")
            
            try:
                input_data = json.loads(input_str)
            except json.JSONDecodeError as e:
                return json.dumps({
                    "error": f"JSON parsing failed: {str(e)}"
                })
        
        # Extract data
        sleep_duration = float(input_data.get('last_sleep_duration', 7))
        busy_hours = input_data.get('busy_hours', [])
        stress_level = int(input_data.get('current_stress_level', 3))
        user_bias = float(input_data.get('user_bias', 0.0))

        # 2. Load Model
        model_path = os.path.join(os.path.dirname(__file__), 'study_model.pkl')
        if not os.path.exists(model_path):
            return json.dumps({"error": "Model file not found. Run train_model.py first."})
        
        model = joblib.load(model_path)

        # 3. Predict 24 Hours
        quality_proxy = min(10, max(1, sleep_duration * 1.2))
        
        # PERSONALIZATION LOGIC:
        # If user_bias is negative (unhappy), they are "Sensitive" -> Punish bad hours harder.
        # If user_bias is positive (happy), they are "Flexible" -> Punish bad hours less.
        sensitivity = 1.0 - (user_bias) # e.g., Bias -0.2 => Sensitivity 1.2 (20% stricter)

        results = []
        for hour in range(24):
            status = "Busy" if hour in busy_hours else "Available"
            
            features = np.array([[sleep_duration, quality_proxy, stress_level]])
            base_score = float(model.predict(features)[0])

            # Personalization
            final_score = base_score + (user_bias * 0.5) # Apply only half bias to base score to prevent saturation

            # Circadian rhythm adjustment
            if 8 <= hour <= 11:
                final_score += 0.1
            elif 13 <= hour <= 15:
                final_score -= (0.15 * sensitivity) # Afternoon slump varies by user sensitivity
            elif 23 <= hour or hour <= 4:
                final_score -= (0.4 * sensitivity) # Late night penalty varies by user sensitivity

            # Clamp to range
            final_score = max(0.0, min(1.0, final_score))

            results.append({
                "hour": hour,
                "formatted_time": f"{hour:02d}:00",
                "score": round(final_score, 2),
                "status": status
            })

        # 4. Get Top 3 Available Hours
        available_hours = [r for r in results if r['status'] == "Available"]
        top_3 = sorted(available_hours, key=lambda x: x['score'], reverse=True)[:3]

        # 5. Output JSON for Laravel
        output = {
            "top_3_hours": top_3,
            "schedule": results,
            "meta": {
                "applied_bias": user_bias,
                "model_source": "Kaggle Sleep Dataset"
            }
        }
        
        return json.dumps(output)

    except Exception as e:
        import traceback
        return json.dumps({
            "error": str(e)
        })

if __name__ == "__main__":
    # NOTE: We use print() only once at the very end
    result = predict()
    print(result)