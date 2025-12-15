# تعليمات إعداد قاعدة البيانات
## Database Setup Instructions

## المشكلة / Problem
إذا ظهرت لك رسالة "حدث خطأ في قاعدة البيانات" عند إرسال نموذج الحجز، فهذا يعني أن بعض الجداول غير موجودة في قاعدة البيانات.

If you see "Database error occurred" when submitting the booking form, it means some tables are missing from your database.

## الحل / Solution

### الخطوة 1: تسجيل الدخول إلى cPanel
1. اذهب إلى cPanel الخاص بك
2. ابحث عن **phpMyAdmin** وافتحه

### Step 1: Login to cPanel
1. Go to your cPanel
2. Find **phpMyAdmin** and open it

---

### الخطوة 2: اختر قاعدة البيانات
1. من القائمة اليمنى، اختر قاعدة البيانات: `u186120816_tantawy`
2. ستظهر لك قائمة بالجداول الموجودة

### Step 2: Select Database
1. From the left sidebar, select the database: `u186120816_tantawy`
2. You'll see a list of existing tables

---

### الخطوة 3: استيراد الملف
1. اضغط على تبويب **Import** (استيراد) في الأعلى
2. اضغط على **Choose File** (اختر ملف)
3. اختر الملف: `database.sql` من مجلد الموقع
4. اضغط على **Go** (تنفيذ) في الأسفل

### Step 3: Import File
1. Click on the **Import** tab at the top
2. Click **Choose File**
3. Select the file: `database.sql` from your website folder
4. Click **Go** at the bottom

---

### الخطوة 4: التحقق
بعد الاستيراد، يجب أن تظهر رسالة نجاح. تأكد من وجود الجداول التالية:

After importing, you should see a success message. Make sure these tables exist:

✅ `admin_users` - مستخدمي الإدارة
✅ `services` - الخدمات
✅ `packages` - الباقات
✅ `bookings` - الحجوزات
✅ `doctors` - الدكاترة
✅ `blog_posts` - المقالات
✅ `reviews` - التقييمات
✅ `settings` - الإعدادات
✅ `sliders` - السلايدر
✅ `email_templates` - قوالب البريد
✅ `menu_items` - عناصر القائمة

---

## ملاحظات مهمة / Important Notes

### بيانات الدخول الافتراضية / Default Admin Login
بعد استيراد قاعدة البيانات، يمكنك تسجيل الدخول إلى لوحة التحكم:

After importing the database, you can login to admin panel:

- **Username:** admin
- **Password:** admin123
- **URL:** https://tantawy.karizmatek.com/admin/

⚠️ **مهم جداً:** غيّر كلمة المرور فوراً بعد أول تسجيل دخول!
⚠️ **Very Important:** Change the password immediately after first login!

---

### إذا كانت الجداول موجودة بالفعل / If Tables Already Exist

إذا كانت بعض الجداول موجودة بالفعل ولا تريد حذفها، يمكنك:

If some tables already exist and you don't want to delete them, you can:

1. استخدام **Browse** لكل جدول ونسخ البيانات الحالية
2. ثم استيراد `database.sql` (سيتم تحديث الجداول فقط)
3. البيانات الموجودة لن تُحذف بفضل استخدام `CREATE TABLE IF NOT EXISTS` و `ON DUPLICATE KEY UPDATE`

1. Use **Browse** for each table and copy current data
2. Then import `database.sql` (will only update tables)
3. Existing data won't be deleted thanks to `CREATE TABLE IF NOT EXISTS` and `ON DUPLICATE KEY UPDATE`

---

## استكشاف الأخطاء / Troubleshooting

### خطأ: "Foreign key constraint fails"
إذا ظهر هذا الخطأ، قم بما يلي:
1. احذف جدول `bookings` إذا كان موجوداً
2. تأكد من وجود جدول `packages` أولاً
3. ثم استورد الملف مرة أخرى

If this error appears:
1. Delete the `bookings` table if it exists
2. Make sure `packages` table exists first
3. Then import the file again

### خطأ: "Table already exists"
هذا ليس خطأ! الملف يستخدم `CREATE TABLE IF NOT EXISTS` لذلك سيتجاهل الجداول الموجودة.

This is not an error! The file uses `CREATE TABLE IF NOT EXISTS` so it will skip existing tables.

---

## الدعم / Support

إذا واجهت أي مشاكل، تحقق من:
- صلاحيات المستخدم في قاعدة البيانات
- حجم الملف لا يتجاوز الحد المسموح في phpMyAdmin
- إعدادات `max_execution_time` في PHP

If you face any issues, check:
- Database user permissions
- File size doesn't exceed phpMyAdmin limits
- PHP `max_execution_time` settings

---

✅ بعد إتمام الخطوات أعلاه، سيعمل نموذج الحجز بشكل صحيح!
✅ After completing the steps above, the booking form will work correctly!
