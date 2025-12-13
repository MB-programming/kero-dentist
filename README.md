# 🦷 موقع حجز عيادة الأسنان | Dentist Booking Website

نظام متكامل لحجز مواعيد عيادة الأسنان مع لوحة تحكم شاملة، مدونة طبية، وإدارة التقييمات.

## 📋 المحتويات | Contents

- [المميزات](#-المميزات--features)
- [التقنيات المستخدمة](#-التقنيات-المستخدمة--technologies)
- [التثبيت والإعداد](#-التثبيت-والإعداد--installation)
- [هيكل المشروع](#-هيكل-المشروع--project-structure)
- [لوحة التحكم](#-لوحة-التحكم--admin-dashboard)
- [إعداد WhatsApp API](#-إعداد-whatsapp-api)
- [إعداد Gmail API](#-إعداد-gmail-api)

---

## ✨ المميزات | Features

### الواجهة الأمامية (Frontend)
- ✅ تصميم عصري وسريع الاستجابة (Responsive Design)
- ✅ صفحة رئيسية تعريفية بالطبيب
- ✅ عرض الخدمات والباقات بأسعارها
- ✅ نموذج حجز مواعيد شامل
- ✅ مدونة طبية مع SEO محسّن
- ✅ عرض تقييمات العملاء
- ✅ واجهة مستخدم باللغة العربية
- ✅ تحسين محركات البحث (SEO)

### لوحة التحكم (Admin Dashboard)
- ✅ لوحة تحكم كاملة بالعربية
- ✅ إدارة الحجوزات مع تحديث الحالات
- ✅ إرسال رسائل واتساب للعملاء
- ✅ إدارة الخدمات والباقات
- ✅ إدارة المقالات مع Rich Text Editor (TinyMCE)
- ✅ إعدادات SEO لكل مقال
- ✅ إدارة التقييمات والموافقة عليها
- ✅ ربط Gmail API لاستقبال التقييمات
- ✅ إحصائيات شاملة
- ✅ إعدادات الموقع

### المميزات التقنية
- ✅ Native Frontend (HTML, CSS, JavaScript)
- ✅ PHP + MySQL Backend
- ✅ نظام آمن للمصادقة
- ✅ حماية من SQL Injection
- ✅ رفع صور آمن
- ✅ نظام SEO متكامل
- ✅ واجهات API RESTful

---

## 🛠 التقنيات المستخدمة | Technologies

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Rich Text Editor**: TinyMCE
- **Icons**: Font Awesome 6
- **Architecture**: MVC-like Pattern

---

## 📥 التثبيت والإعداد | Installation

### المتطلبات | Requirements

- PHP 7.4 أو أحدث
- MySQL 5.7 أو أحدث
- Apache/Nginx Web Server
- Composer (اختياري)

### خطوات التثبيت | Installation Steps

1. **استنساخ المشروع | Clone the Project**
```bash
git clone https://github.com/your-username/kero-dentist.git
cd kero-dentist
```

2. **إنشاء قاعدة البيانات | Create Database**
```bash
# افتح MySQL
mysql -u root -p

# قم بتنفيذ ملف قاعدة البيانات
mysql -u root -p < database.sql
```

3. **تحديث ملف الإعدادات | Update Configuration**

افتح ملف `includes/config.php` وقم بتحديث بيانات الاتصال:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'dentist_booking');
define('SITE_URL', 'http://your-domain.com');
```

4. **ضبط الصلاحيات | Set Permissions**
```bash
chmod 755 public/uploads
chmod 755 public/uploads/blog
chmod 755 public/uploads/services
```

5. **الوصول للموقع | Access the Website**

- **الموقع الرئيسي**: `http://your-domain.com/public/`
- **لوحة التحكم**: `http://your-domain.com/admin/`

### بيانات الدخول الافتراضية | Default Login
```
Username: admin
Password: admin123
```

**⚠️ مهم**: قم بتغيير كلمة المرور فوراً بعد التثبيت!

---

## 📁 هيكل المشروع | Project Structure

```
kero-dentist/
├── public/                 # الواجهة الأمامية
│   ├── index.php          # الصفحة الرئيسية
│   ├── blog.php           # قائمة المقالات
│   ├── blog-post.php      # صفحة المقال
│   ├── css/
│   │   └── style.css      # تنسيقات الموقع
│   ├── js/
│   │   └── main.js        # JavaScript الرئيسي
│   ├── images/            # صور الموقع
│   └── uploads/           # الملفات المرفوعة
│       ├── blog/          # صور المقالات
│       └── services/      # صور الخدمات
│
├── admin/                  # لوحة التحكم
│   ├── index.php          # الرئيسية
│   ├── login.php          # تسجيل الدخول
│   ├── bookings.php       # إدارة الحجوزات
│   ├── blog.php           # إدارة المقالات
│   ├── blog-edit.php      # تعديل المقالات
│   ├── reviews.php        # إدارة التقييمات
│   ├── settings.php       # الإعدادات
│   ├── css/
│   │   └── admin.css      # تنسيقات لوحة التحكم
│   ├── js/
│   │   └── admin.js       # JavaScript لوحة التحكم
│   └── includes/
│       ├── header.php     # رأس الصفحة
│       └── footer.php     # تذييل الصفحة
│
├── api/                    # واجهات API
│   ├── submit-booking.php
│   ├── get-booking.php
│   └── mark-whatsapp-sent.php
│
├── includes/
│   └── config.php         # إعدادات النظام
│
├── database.sql           # ملف قاعدة البيانات
├── README.md              # هذا الملف
├── WHATSAPP_API_SETUP.md  # دليل إعداد WhatsApp
└── GMAIL_API_SETUP.md     # دليل إعداد Gmail
```

---

## 🎛 لوحة التحكم | Admin Dashboard

### الصفحة الرئيسية
- عرض الإحصائيات العامة
- آخر الحجوزات
- التقييمات التي تحتاج موافقة

### إدارة الحجوزات
- عرض جميع الحجوزات
- تصفية حسب الحالة
- البحث بالاسم أو الهاتف
- تحديث حالة الحجز
- إرسال رسالة واتساب للعميل
- حذف الحجوزات

### إدارة المقالات
- إضافة مقالات جديدة
- Rich Text Editor (TinyMCE)
- رفع صور للمقالات
- إعدادات SEO لكل مقال:
  - Meta Title
  - Meta Description
  - Meta Keywords
- النشر/إلغاء النشر
- تعديل وحذف المقالات

### إدارة التقييمات
- عرض جميع التقييمات
- الموافقة/الرفض
- إضافة تقييمات يدوية
- ربط Gmail API

### الإعدادات
- معلومات الموقع
- معلومات الطبيب
- إعدادات WhatsApp API
- إعدادات Gmail API

---

## 📱 إعداد WhatsApp API

لإرسال رسائل واتساب للعملاء، تحتاج إلى:

### الخيار 1: استخدام رابط WhatsApp مباشر (الحالي)
النظام الحالي يستخدم رابط WhatsApp مباشر (`wa.me`) الذي يفتح محادثة مع العميل.

### الخيار 2: Twilio WhatsApp API

1. **إنشاء حساب Twilio**
   - سجل في https://www.twilio.com
   - احصل على Account SID و Auth Token

2. **تفعيل WhatsApp**
   - من لوحة تحكم Twilio، فعّل WhatsApp
   - احصل على WhatsApp-enabled phone number

3. **التكامل مع الموقع**
   ```php
   // في ملف api/send-whatsapp.php
   $account_sid = 'your_account_sid';
   $auth_token = 'your_auth_token';
   $twilio_number = 'whatsapp:+14155238886';
   ```

راجع ملف `WHATSAPP_API_SETUP.md` للتفاصيل الكاملة.

---

## 📧 إعداد Gmail API

لاستقبال التقييمات تلقائياً عبر البريد الإلكتروني:

### الخطوات:

1. **إنشاء مشروع في Google Cloud Console**
   - اذهب إلى https://console.cloud.google.com
   - أنشئ مشروع جديد

2. **تفعيل Gmail API**
   - في المشروع، فعّل Gmail API

3. **إنشاء OAuth 2.0 Credentials**
   - احصل على Client ID و Client Secret

4. **إضافة البيانات في الإعدادات**
   - من لوحة التحكم > الإعدادات
   - أضف Gmail API Client ID و Secret

راجع ملف `GMAIL_API_SETUP.md` للتفاصيل الكاملة.

---

## 📊 قاعدة البيانات | Database

### الجداول الرئيسية:

- **admin_users**: مستخدمي لوحة التحكم
- **services**: الخدمات المقدمة
- **packages**: الباقات والأسعار
- **bookings**: الحجوزات
- **blog_posts**: المقالات
- **reviews**: التقييمات
- **settings**: إعدادات الموقع

---

## 🔐 الأمان | Security

- ✅ تشفير كلمات المرور (bcrypt)
- ✅ حماية من SQL Injection (Prepared Statements)
- ✅ تنظيف المدخلات (Input Sanitization)
- ✅ حماية من XSS
- ✅ نظام جلسات آمن
- ✅ التحقق من صلاحيات الوصول

---

## 🎨 التخصيص | Customization

### تغيير الألوان:

افتح `public/css/style.css` و `admin/css/admin.css` وعدّل:

```css
:root {
    --primary-color: #2563eb;      /* اللون الأساسي */
    --primary-dark: #1e40af;       /* اللون الأساسي الداكن */
    --secondary-color: #10b981;    /* اللون الثانوي */
}
```

### تعديل المحتوى:
- الإعدادات العامة: من لوحة التحكم > الإعدادات
- الخدمات والباقات: من لوحة التحكم > الخدمات/الباقات
- المحتوى الثابت: عدّل الملفات في `public/`

---

## 📝 الترخيص | License

هذا المشروع مفتوح المصدر ومتاح للاستخدام الحر.

---

## 🤝 المساهمة | Contributing

المساهمات مرحب بها! يمكنك:
- الإبلاغ عن مشاكل (Issues)
- اقتراح ميزات جديدة
- إرسال Pull Requests

---

## 📞 الدعم | Support

للأسئلة والاستفسارات:
- افتح Issue في GitHub
- راجع ملفات التوثيق

---

## 🎯 الميزات القادمة | Upcoming Features

- [ ] نظام دفع إلكتروني
- [ ] تطبيق الهاتف المحمول
- [ ] نظام الإشعارات
- [ ] تقارير وإحصائيات متقدمة
- [ ] نظام المواعيد التلقائي
- [ ] دعم لغات متعددة

---

Made with ❤️ for Dentists
