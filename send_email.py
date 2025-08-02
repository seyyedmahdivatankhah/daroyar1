import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def send_email(to_email, subject, message):
    sender_email = "s.mahdiworkdevelop@gmail.com"  # ایمیل شما
    sender_password = "@Mahdi123456789"  # رمز عبور یا App Password

    # تنظیم محتوای ایمیل
    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = to_email
    msg['Subject'] = subject
    msg.attach(MIMEText(message, 'plain'))

    try:
        # اتصال به سرور Gmail (استفاده از SSL برای امنیت بیشتر)
        server = smtplib.SMTP_SSL('smtp.gmail.com', 465)
        server.login(sender_email, sender_password)  # ورود به ایمیل
        server.send_message(msg)  # ارسال ایمیل
        print(f"✅ ایمیل به {to_email} ارسال شد!")
    except Exception as e:
        print(f"❌ خطا در ارسال ایمیل: {e}")
    finally:
        server.quit()

# تست ارسال ایمیل
send_email("user@example.com", "یادآوری مصرف دارو", "یادت نره داروهاتو مصرف کنی!")
