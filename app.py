from flask import Flask, render_template, request, redirect, url_for, flash
import pandas as pd
import pickle
import os
import re

app = Flask(__name__)
app.secret_key = 'supersecretkey'

# دریافت مسیر مطلق فایل‌ها
model_path = os.path.join(os.path.dirname(__file__), 'disease_model.pkl')
dataset_path = os.path.join(os.path.dirname(__file__), 'medical_dataset.csv')

# بررسی وجود فایل‌ها و بارگذاری آن‌ها
if not os.path.exists(model_path):
    raise FileNotFoundError("❌ فایل مدل (disease_model.pkl) یافت نشد!")
if not os.path.exists(dataset_path):
    raise FileNotFoundError("❌ فایل دیتاست (medical_dataset.csv) یافت نشد!")

# بارگذاری مدل ذخیره‌شده
with open(model_path, 'rb') as model_file:
    try:
        model = pickle.load(model_file)
    except Exception as e:
        raise Exception(f"⚠️ خطا در بارگذاری مدل: {e}")

# بارگذاری دیتاست
try:
    medical_df = pd.read_csv(dataset_path)
except Exception as e:
    raise Exception(f"⚠️ خطا در بارگذاری دیتاست: {e}")

# پاکسازی متن ورودی
def clean_text(text):
    text = text.lower()
    text = re.sub(r'[^a-zA-Z0-9\s]', '', text)
    return text

# تشخیص بیماری و دارو از دیتاست
def diagnose(symptoms):
    for index, row in medical_df.iterrows():
        csv_symptoms = [row['symptom1'], row['symptom2'], row['symptom3']]
        if all(symptom in csv_symptoms for symptom in symptoms):
            return row['disease'], row['medication']
    return "تشخیص داده نشد", "-"

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/online_consultant', methods=['GET', 'POST'])
def online_consultant():
    if request.method == 'POST':
        symptom_text = request.form.get('mainComplaint')
        symptom1 = request.form.get('symptom1')
        symptom2 = request.form.get('symptom2')
        symptom3 = request.form.get('symptom3')

        # بررسی ورودی‌ها
        if not symptom_text and not (symptom1 and symptom2 and symptom3):
            flash('لطفاً علائم خود را کامل وارد کنید.')
            return redirect(url_for('online_consultant'))

        # روش اول: استفاده از مدل
        cleaned_text = clean_text(symptom_text)
        try:
            predicted_disease = model.predict([cleaned_text])[0]
        except Exception as e:
            flash(f'خطا در پردازش اطلاعات توسط مدل: {e}')
            return redirect(url_for('online_consultant'))

        # روش دوم: تطابق با دیتاست
        symptoms = [symptom1, symptom2, symptom3]
        dataset_disease, medication = diagnose(symptoms)

        return render_template('result.html', 
                               model_disease=predicted_disease, 
                               dataset_disease=dataset_disease, 
                               medication=medication)

    return render_template('online_consultant.html')

if __name__ == '__main__':
    print("✅ برنامه با موفقیت اجرا شد. به http://127.0.0.1:5000 بروید.")
    app.run(debug=True)
