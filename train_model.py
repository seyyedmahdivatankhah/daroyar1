import pandas as pd
import pickle
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.metrics import accuracy_score

# 1. بارگذاری داده‌های آموزشی
data = pd.read_csv('medical_dataset.csv')

# 2. اعتبارسنجی ستون‌ها
required_columns = {'symptom1', 'symptom2', 'symptom3', 'disease'}
if not required_columns.issubset(data.columns):
    raise ValueError("برخی از ستون‌های ضروری در فایل CSV موجود نیستند.")

# 3. ترکیب علائم
data['symptoms'] = data[['symptom1', 'symptom2', 'symptom3']].fillna('').agg(' '.join, axis=1)

# 4. تقسیم داده‌ها به ویژگی‌ها و برچسب‌ها
X = data['symptoms']
y = data['disease']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 5. ساخت مدل
model = Pipeline([
    ('tfidf', TfidfVectorizer(stop_words='english')),
    ('clf', MultinomialNB(alpha=0.1))
])

# 6. آموزش مدل
model.fit(X_train, y_train)

# 7. ارزیابی مدل
accuracy = accuracy_score(y_test, model.predict(X_test))
print(f"دقت مدل: {accuracy:.2f}")

# 8. ذخیره مدل
model_path = 'disease_model.pkl'
with open(model_path, 'wb') as model_file:
    pickle.dump(model, model_file)

print(f"مدل با موفقیت در {model_path} ذخیره شد.")
