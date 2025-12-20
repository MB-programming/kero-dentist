# قاعدة بيانات الحجوزات

## 📋 نظرة عامة

قاعدة بيانات خاصة بحجوزات العيادة مع جداول منفصلة للحفاظ على تنظيم البيانات.

**اسم قاعدة البيانات:** `u186120816_kero_dentist`
**اسم المستخدم:** `u186120816_kero_dentist`

---

## 🚀 طريقة التثبيت

### الطريقة الأولى: phpMyAdmin

1. افتح phpMyAdmin
2. اضغط على "استيراد" (Import)
3. اختر الملف `booking_database.sql`
4. اضغط "تنفيذ" (Execute)

### الطريقة الثانية: سطر الأوامر (Terminal)

```bash
# تسجيل الدخول إلى MySQL
mysql -u root -p

# استيراد الملف
source /path/to/kero-dentist/database/booking_database.sql

# أو مباشرة:
mysql -u root -p < /path/to/kero-dentist/database/booking_database.sql
```

### الطريقة الثالثة: تلقائياً من الكود

الملف `api/booking-db-connection.php` يقوم بإنشاء قاعدة البيانات تلقائياً عند أول استخدام!

---

## 📊 جداول قاعدة البيانات

### 1️⃣ جدول `bookings` - الحجوزات

الجدول الرئيسي لحفظ جميع الحجوزات.

**الحقول الرئيسية:**
- `id` - رقم الحجز التلقائي
- `client_name` - اسم العميل
- `client_phone` - رقم الهاتف
- `client_phone_carrier` - شركة الهاتف (Vodafone, Orange, إلخ)
- `client_whatsapp` - رقم الواتساب
- `client_whatsapp_carrier` - شركة الواتساب
- `booking_date` - تاريخ الحجز
- `booking_time` - وقت الحجز
- `package_name` - اسم الباقة
- `package_price` - سعر الباقة
- `status` - حالة الحجز (pending, approved, completed, cancelled)

**الفهارس:**
- فهرس على `status` للاستعلامات السريعة
- فهرس على `booking_date` للبحث بالتاريخ
- فهرس على `client_phone` للبحث بالرقم

### 2️⃣ جدول `packages` - الباقات

نسخة من باقات الخدمات للمرجعية.

### 3️⃣ جدول `booking_history` - سجل التغييرات

يحفظ تلقائياً كل تغيير يحدث على حالة الحجز.

**مثال:**
```
Booking #123: pending → approved (by: admin)
Booking #123: approved → completed (by: doctor)
```

### 4️⃣ جدول `booking_stats` - الإحصائيات

إحصائيات يومية للحجوزات والإيرادات.

---

## 🔍 Views (الاستعلامات الجاهزة)

### 1. `active_bookings` - الحجوزات النشطة

```sql
SELECT * FROM active_bookings;
```
يعرض جميع الحجوزات المعلقة والمؤكدة القادمة.

### 2. `today_bookings` - حجوزات اليوم

```sql
SELECT * FROM today_bookings;
```
يعرض حجوزات اليوم فقط مرتبة حسب الوقت.

### 3. `booking_summary` - ملخص الحجوزات

```sql
SELECT * FROM booking_summary;
```
يعرض إحصائيات شاملة (إجمالي، معلقة، مكتملة، ملغاة، الإيرادات).

### 4. `popular_carriers` - أكثر الشركات استخداماً

```sql
SELECT * FROM popular_carriers;
```
يعرض أكثر شركات الاتصالات استخداماً من العملاء.

---

## ⚙️ Stored Procedures (الإجراءات المخزنة)

### 1. تحديث حالة الحجز

```sql
CALL update_booking_status(
    123,                    -- رقم الحجز
    'approved',            -- الحالة الجديدة
    'admin',               -- من قام بالتغيير
    'تم الموافقة'         -- ملاحظات
);
```

### 2. إحصائيات شهرية

```sql
-- إحصائيات ديسمبر 2025
CALL get_monthly_stats(2025, 12);
```

### 3. الحجوزات القادمة

```sql
-- الحجوزات في الـ 7 أيام القادمة
CALL get_upcoming_bookings(7);
```

---

## 🔧 Triggers (المشغلات التلقائية)

### `after_booking_status_update`

يعمل تلقائياً عند تغيير حالة الحجز ويحفظ التغيير في `booking_history`.

**مثال:**
```sql
-- عند تنفيذ هذا:
UPDATE bookings SET status = 'approved' WHERE id = 123;

-- يتم تلقائياً إضافة سجل في booking_history
```

---

## 📊 استعلامات مفيدة

### الحجوزات المعلقة اليوم

```sql
SELECT *
FROM bookings
WHERE status = 'pending'
  AND booking_date = CURDATE()
ORDER BY booking_time;
```

### إجمالي الإيرادات هذا الشهر

```sql
SELECT
    COUNT(*) AS total_bookings,
    SUM(package_price) AS total_revenue
FROM bookings
WHERE status = 'completed'
  AND YEAR(created_at) = YEAR(CURDATE())
  AND MONTH(created_at) = MONTH(CURDATE());
```

### أكثر الباقات حجزاً

```sql
SELECT
    package_name,
    COUNT(*) AS booking_count,
    SUM(package_price) AS revenue
FROM bookings
WHERE status = 'completed'
GROUP BY package_name
ORDER BY booking_count DESC
LIMIT 5;
```

### الحجوزات حسب الشركة

```sql
SELECT
    client_phone_carrier AS carrier,
    COUNT(*) AS count
FROM bookings
GROUP BY client_phone_carrier
ORDER BY count DESC;
```

### متوسط قيمة الحجز

```sql
SELECT
    AVG(package_price) AS avg_booking_value,
    MIN(package_price) AS min_value,
    MAX(package_price) AS max_value
FROM bookings
WHERE status = 'completed';
```

---

## 🔒 الأمان

### النسخ الاحتياطي

```bash
# نسخة احتياطية كاملة
mysqldump -u u186120816_kero_dentist -p u186120816_kero_dentist > backup_$(date +%Y%m%d).sql

# نسخة احتياطية للحجوزات فقط
mysqldump -u u186120816_kero_dentist -p u186120816_kero_dentist bookings > bookings_backup.sql
```

### الاستعادة

```bash
# استعادة من نسخة احتياطية
mysql -u u186120816_kero_dentist -p u186120816_kero_dentist < backup_20251220.sql
```

### صلاحيات المستخدم

```sql
-- إنشاء مستخدم خاص بقاعدة البيانات
CREATE USER 'booking_user'@'localhost' IDENTIFIED BY 'strong_password';

-- منح الصلاحيات
GRANT SELECT, INSERT, UPDATE ON kero_dentist_bookings.* TO 'booking_user'@'localhost';

-- تطبيق التغييرات
FLUSH PRIVILEGES;
```

---

## 📈 الإحصائيات والتقارير

### Dashboard سريع

```sql
SELECT
    'Total Bookings' AS metric,
    COUNT(*) AS value
FROM bookings
UNION ALL
SELECT
    'Pending',
    COUNT(*)
FROM bookings
WHERE status = 'pending'
UNION ALL
SELECT
    'Completed',
    COUNT(*)
FROM bookings
WHERE status = 'completed'
UNION ALL
SELECT
    'Total Revenue',
    SUM(package_price)
FROM bookings
WHERE status = 'completed';
```

### الحجوزات حسب اليوم

```sql
SELECT
    booking_day,
    COUNT(*) AS count
FROM bookings
GROUP BY booking_day
ORDER BY count DESC;
```

---

## 🗑️ حذف البيانات التجريبية

بعد التثبيت، احذف البيانات التجريبية:

```sql
-- حذف الحجوزات التجريبية
DELETE FROM bookings WHERE id <= 4;

-- حذف الباقات التجريبية (اختياري)
-- DELETE FROM packages WHERE id <= 6;

-- إعادة ترقيم الـ AUTO_INCREMENT
ALTER TABLE bookings AUTO_INCREMENT = 1;
```

---

## 🔧 الصيانة

### تحسين الجداول

```sql
OPTIMIZE TABLE bookings;
OPTIMIZE TABLE booking_history;
```

### تحليل الأداء

```sql
ANALYZE TABLE bookings;
```

### إصلاح الجداول

```sql
REPAIR TABLE bookings;
```

---

## 📞 شركات الاتصالات المدعومة

| الشركة | البريفكس | مثال |
|--------|---------|------|
| Vodafone | 010, 011 | 0101 234 5678 |
| Etisalat | 011 | 0114 567 8901 |
| Orange | 012 | 0122 345 6789 |
| WE | 015 | 0155 678 9012 |

---

## ❓ الأسئلة الشائعة

### س: لماذا قاعدة بيانات منفصلة؟

**ج:** للأسباب التالية:
1. **الأمان**: عزل بيانات العملاء الحساسة
2. **الأداء**: استعلامات الحجوزات لا تؤثر على الموقع
3. **النسخ الاحتياطي**: يمكن عمل backup منفصل
4. **الصلاحيات**: يمكن منح صلاحيات محددة

### س: هل يتم إنشاء قاعدة البيانات تلقائياً؟

**ج:** نعم! ملف `api/booking-db-connection.php` ينشئ قاعدة البيانات والجداول تلقائياً عند أول حجز.

### س: كيف أرى الحجوزات في الأدمن؟

**ج:** الحجوزات تظهر في `admin/bookings.php` من قاعدة البيانات الرئيسية. لكن يمكنك ربط قاعدتي البيانات باستخدام:

```sql
-- في قاعدة البيانات الرئيسية
SELECT * FROM kero_dentist_bookings.bookings;
```

---

## 📝 ملاحظات مهمة

1. ✅ تأكد من تحديث بيانات الاتصال في `api/booking-db-connection.php`
2. ✅ احذف البيانات التجريبية قبل الإنتاج
3. ✅ اعمل نسخة احتياطية دورية
4. ✅ استخدم كلمة مرور قوية لقاعدة البيانات
5. ✅ راقب حجم قاعدة البيانات بانتظام

---

## 🎯 الخلاصة

قاعدة البيانات جاهزة للاستخدام مباشرة بعد التثبيت!

تحتوي على:
- ✅ 4 جداول رئيسية
- ✅ 4 Views للاستعلامات السريعة
- ✅ 3 Stored Procedures للعمليات الشائعة
- ✅ Triggers تلقائية لحفظ السجلات
- ✅ بيانات تجريبية للاختبار

---

**تم إنشاء هذا الملف بواسطة:** Claude AI
**التاريخ:** ديسمبر 2025
**النسخة:** 1.0
