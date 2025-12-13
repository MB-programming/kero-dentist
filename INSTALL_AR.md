# 🚀 دليل التثبيت السريع | Quick Installation Guide

## ⚠️ مهم جداً | IMPORTANT

قبل أي شيء، **لازم تحدّث كلمة مرور قاعدة البيانات** في الملف:

```
includes/config.php
```

**السطر 5:**
```php
define('DB_PASS', '');  // ⚠️ ضع كلمة مرور قاعدة البيانات هنا
```

---

## 📋 خطوات التثبيت | Installation Steps

### الطريقة 1: استخدام Setup (الأسهل) 🎯

1. **افتح المتصفح وروح على:**
   ```
   http://tantawy.karizmatek.com/setup.php
   ```

2. **أدخل بيانات قاعدة البيانات:**
   - اسم الخادم: `localhost`
   - اسم المستخدم: `u186120816_tantawy`
   - كلمة المرور: **[كلمة مرور قاعدة البيانات]**
   - اسم قاعدة البيانات: `u186120816_tantawy`

3. **اضغط "ابدأ التثبيت"**

4. **بعد التثبيت:**
   - احذف ملفات `setup.php` و `check-setup.php`
   - افتح لوحة التحكم

---

### الطريقة 2: يدوياً (Manual)

1. **أدخل على cPanel أو phpMyAdmin**

2. **استورد ملف قاعدة البيانات:**
   - افتح قاعدة البيانات: `u186120816_tantawy`
   - استورد الملف: `database.sql`

3. **حدّث ملف الإعدادات:**
   - افتح `includes/config.php`
   - ضع كلمة مرور قاعدة البيانات في السطر 5

4. **تأكد من الصلاحيات:**
   ```bash
   chmod 755 public/uploads
   chmod 755 public/uploads/blog
   chmod 755 public/uploads/services
   ```

---

## 🔍 فحص الإعدادات | Check Setup

**لفحص أن كل شيء شغال:**

افتح:
```
http://tantawy.karizmatek.com/check-setup.php
```

هيفحص:
- ✅ إصدار PHP
- ✅ PHP Extensions
- ✅ اتصال قاعدة البيانات
- ✅ الجداول الموجودة
- ✅ صلاحيات الملفات

---

## 🎛 الوصول للوحة التحكم | Admin Access

**الرابط:**
```
http://tantawy.karizmatek.com/admin/
```

**بيانات الدخول الافتراضية:**
```
Username: admin
Password: admin123
```

### ⚠️ **مهم جداً:**
**غيّر كلمة المرور فوراً بعد تسجيل الدخول!**

---

## 🏠 الموقع الأمامي | Public Website

**الرابط:**
```
http://tantawy.karizmatek.com/public/
```

أو ببساطة:
```
http://tantawy.karizmatek.com/
```

---

## 🔧 حل المشاكل | Troubleshooting

### المشكلة: HTTP ERROR 500

**الأسباب المحتملة:**

1. **كلمة مرور قاعدة البيانات فاضية**
   - حل: افتح `includes/config.php`
   - ضع كلمة المرور في السطر 5

2. **قاعدة البيانات مش موجودة**
   - حل: استورد `database.sql`
   - أو استخدم `setup.php`

3. **الجداول مش موجودة**
   - حل: استورد `database.sql`

4. **مشكلة في صلاحيات PHP**
   - حل: تحقق من `.htaccess` و `.user.ini`

---

### المشكلة: الصور ما بتتحمّل

**الحل:**
```bash
chmod 755 public/uploads
chmod 755 public/uploads/blog
chmod 755 public/uploads/services
```

---

### المشكلة: الموقع ما بيفتح

**تحقق من:**
1. الدومين صح: `tantawy.karizmatek.com`
2. الملفات موجودة في المسار الصحيح
3. ملف `.htaccess` موجود

---

## 📁 هيكل الملفات | File Structure

```
/
├── index.php              # التوجيه الرئيسي
├── setup.php              # برنامج التثبيت (احذفه بعد التثبيت)
├── check-setup.php        # فحص الإعدادات (احذفه بعد التثبيت)
├── database.sql           # ملف قاعدة البيانات
│
├── public/                # الموقع الأمامي
│   ├── index.php
│   ├── blog.php
│   └── uploads/           # مجلد رفع الملفات
│
├── admin/                 # لوحة التحكم
│   ├── login.php
│   ├── index.php
│   └── ...
│
└── includes/
    └── config.php         # ⚠️ مهم: ضع كلمة المرور هنا

```

---

## ✅ قائمة المراجعة | Checklist

قبل ما تبدأ، تأكد من:

- [ ] وضعت كلمة مرور قاعدة البيانات في `config.php`
- [ ] استوردت ملف `database.sql`
- [ ] الصلاحيات صحيحة على `public/uploads`
- [ ] فحصت الموقع عن طريق `check-setup.php`
- [ ] دخلت على لوحة التحكم بنجاح
- [ ] غيّرت كلمة مرور الأدمن الافتراضية
- [ ] حذفت `setup.php` و `check-setup.php`

---

## 🆘 المساعدة | Support

**لو عندك مشكلة:**

1. افتح `check-setup.php` لفحص الإعدادات
2. شوف رسالة الخطأ اللي ظاهرة
3. تأكد من كلمة مرور قاعدة البيانات
4. تأكد من استيراد `database.sql`

---

## 🎉 بعد التثبيت | After Installation

**الخطوات التالية:**

1. ✅ غيّر كلمة مرور الأدمن
2. ✅ حدّث إعدادات الموقع من لوحة التحكم
3. ✅ أضف الخدمات والباقات
4. ✅ ابدأ استقبال الحجوزات!

---

**تم التثبيت؟ ممتاز! 🎊**

احذف ملفات التثبيت للأمان:
- `setup.php`
- `check-setup.php`
- `INSTALL_AR.md` (هذا الملف)

---

Made with ❤️ for Dentists
