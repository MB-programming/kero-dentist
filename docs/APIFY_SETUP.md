# إعداد Apify API للتقييمات

## الخطوة 1: إضافة API Token

اذهب إلى `/admin/settings.php` والصق Apify API Token في حقل "Apify API Token".

**ملاحظة**: تم إرسال API Token الخاص بك عبر الرسائل.

## الخطوة 2: تفعيل التحديث التلقائي

1. في `/admin/settings.php`
2. اختر "Apify API" من قائمة "طريقة التحديث التلقائي"
3. احفظ الإعدادات

## الخطوة 3: استيراد التقييمات

1. اذهب إلى `/admin/import_reviews.php`
2. اضغط على زر "جلب التقييمات من Apify الآن"
3. انتظر 1-2 دقيقة
4. ستظهر جميع الـ 129 تقييم تلقائياً!

## الخطوة 4: إعداد Cron Job (اختياري)

لتحديث التقييمات تلقائياً كل 3 أيام، أضف هذا السطر لـ crontab:

```bash
0 2 */3 * * /usr/bin/php /home/user/kero-dentist/cron/auto_update_reviews.php >> /home/user/kero-dentist/cron/reviews_update.log 2>&1
```

## إضافة مباشرة في قاعدة البيانات (بديل)

إذا أردت إضافة API Token مباشرة في قاعدة البيانات، شغّل هذا SQL:

```sql
INSERT INTO settings (setting_key, setting_value, created_at, updated_at)
VALUES ('apify_api_key', 'YOUR_APIFY_TOKEN_HERE', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    setting_value = 'YOUR_APIFY_TOKEN_HERE',
    updated_at = NOW();

-- تفعيل Apify كطريقة التحديث
INSERT INTO settings (setting_key, setting_value, created_at, updated_at)
VALUES ('review_update_method', 'apify', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    setting_value = 'apify',
    updated_at = NOW();
```

استبدل `YOUR_APIFY_TOKEN_HERE` بالمفتاح الخاص بك.

## التحقق من النجاح

بعد الاستيراد، اذهب إلى:
- `/admin/google_reviews.php` - لرؤية التقييمات في لوحة التحكم
- `/reviews.php` - لرؤية التقييمات في الموقع
- `/` (الصفحة الرئيسية) - لرؤية التقييمات في قسم "آراء العملاء"

## استكشاف الأخطاء

### "يرجى إضافة Apify API Key"
- تأكد من إضافة المفتاح في `/admin/settings.php`
- تحقق من حفظ الإعدادات

### "انتهت المهلة"
- حاول مرة أخرى (قد يكون Apify مشغول)
- تحقق من اتصال الإنترنت

### "خطأ في Apify API"
- تحقق من صحة API Token
- تحقق من رصيد Apify ($5 credit)
