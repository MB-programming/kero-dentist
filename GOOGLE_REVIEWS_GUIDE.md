# دليل استيراد تقييمات Google Maps (129 تقييم)

## 🎯 الهدف
استيراد جميع التقييمات (129 تقييم) من Google Maps بدون API مع تحديث تلقائي كل 3 أيام.

---

## 📋 الخيارات المتاحة

### ✅ الخيار 1: Outscraper (موصى به - سهل وتلقائي)

**المميزات:**
- ✅ **مجاني**: 100 request شهرياً
- ✅ **تلقائي بالكامل**: Cron job يحدث كل 3 أيام
- ✅ **جودة عالية**: بيانات كاملة مع الصور

**الخطوات:**

#### 1. إنشاء حساب Outscraper
```
1. اذهب إلى: https://app.outscraper.com/signup
2. سجل حساب مجاني (بالبريد الإلكتروني أو Google)
3. تأكد من البريد الإلكتروني
```

#### 2. الحصول على API Key
```
1. اذهب إلى: https://app.outscraper.com/api-settings
2. انسخ "API Key" من الصفحة
3. احفظه في مكان آمن
```

#### 3. جلب التقييمات (أول مرة - يدوياً)
```
1. افتح: https://app.outscraper.com/google-maps-reviews-scraper
2. في حقل "Query"، الصق:
   https://maps.app.goo.gl/9TCoYUMWk6kHKwqd8

3. اضبط الإعدادات:
   - Reviews Limit: 150
   - Language: ar
   - Sort: newest
   - Cutoff Rating: 3 (لجلب فقط 3-5 نجوم)

4. اضغط "Start"
5. انتظر 1-2 دقيقة
6. حمّل النتيجة بصيغة JSON
```

#### 4. استيراد في الموقع
```
1. اذهب إلى: /admin/import_reviews.php
2. انسخ محتوى ملف JSON
3. الصق في المربع "استيراد من JSON"
4. اضغط "استيراد التقييمات"
✅ تم! جميع التقييمات ستظهر في الموقع
```

#### 5. تفعيل التحديث التلقائي
```
1. اذهب إلى: /admin/settings.php
2. ابحث عن قسم "التحديث التلقائي للتقييمات"
3. اختر: "Outscraper API"
4. الصق API Key في الحقل
5. احفظ الإعدادات
```

#### 6. إعداد Cron Job
```bash
# فتح crontab
crontab -e

# إضافة هذا السطر:
0 2 */3 * * /usr/bin/php /home/user/kero-dentist/cron/auto_update_reviews.php >> /home/user/kero-dentist/cron/reviews_update.log 2>&1

# حفظ والخروج
```

**✅ انتهى! الآن:**
- جميع الـ 129 تقييم موجودين في الموقع
- كل 3 أيام سيجلب التقييمات الجديدة تلقائياً
- فقط التقييمات 3-5 نجوم ستظهر

---

### ✅ الخيار 2: Google Sheets + Apps Script (مجاني 100%)

**المميزات:**
- ✅ **مجاني 100%**: بدون حدود
- ✅ **تحديث سهل**: تحديث الشيت وسيتحدث الموقع تلقائياً
- ⚠️ **يدوي جزئياً**: تحتاج تحديث الشيت بنفسك

**الخطوات:**

#### 1. إنشاء Google Sheet
```
1. افتح: https://docs.google.com/spreadsheets/create
2. سمّه: "Kero Dentist Reviews"
```

#### 2. إضافة Apps Script
```
1. اذهب إلى: Extensions > Apps Script
2. امسح الكود الموجود
3. الصق الكود من الملف: google_apps_script.gs (في المرفقات)
4. عدّل PLACE_ID إلى: CTFqEnDtDuxAEAE
5. احفظ: File > Save (Ctrl+S)
```

#### 3. تشغيل السكريبت
```
1. اختر function: getGoogleReviews
2. اضغط Run (زر التشغيل)
3. سيطلب منك إذن - اقبل
4. انتظر دقيقة
✅ ستملأ الشيت بجميع التقييمات!
```

#### 4. نشر الشيت كـ CSV
```
1. اذهب إلى: File > Share > Publish to web
2. اختر: الشيت الأول، CSV
3. اضغط Publish
4. انسخ الرابط (سيبدأ بـ https://docs.google.com/...)
```

#### 5. ربط الموقع بالشيت
```
1. اذهب إلى: /admin/settings.php
2. في قسم "التحديث التلقائي"
3. اختر: "Google Sheets CSV"
4. الصق رابط CSV
5. احفظ
```

#### 6. إعداد Cron Job
```bash
crontab -e

# نفس السطر السابق:
0 2 */3 * * /usr/bin/php /home/user/kero-dentist/cron/auto_update_reviews.php >> /home/user/kero-dentist/cron/reviews_update.log 2>&1
```

**✅ انتهى!**
- التقييمات ستتحدث من الشيت كل 3 أيام
- لتحديث يدوي: شغّل السكريبت في الشيت مرة أخرى

---

### ✅ الخيار 3: Apify (بديل آخر)

**رابط:** https://apify.com/compass/google-maps-reviews-scraper

**الخطوات:**
1. سجل حساب ($5 credit مجاناً)
2. شغّل Actor مع Place ID: `CTFqEnDtDuxAEAE`
3. حمّل JSON
4. استورد في `/admin/import_reviews.php`

---

## 🔧 استكشاف الأخطاء

### ❌ "فشل الاستيراد"
**الحل:**
- تأكد من تنسيق JSON صحيح
- تحقق من وجود حقول `author_name` و `rating`

### ❌ "Outscraper API Error 401"
**الحل:**
- تأكد من API Key صحيح
- تحقق من الحساب ما زال فيه credits

### ❌ "Cron Job لا يعمل"
**الحل:**
```bash
# تحقق من crontab
crontab -l

# شغّل يدوياً للاختبار
/usr/bin/php /home/user/kero-dentist/cron/auto_update_reviews.php

# راجع الـ log
tail -f /home/user/kero-dentist/cron/reviews_update.log
```

### ❌ "Google Sheets فارغ"
**الحل:**
- تحقق من إذن Apps Script
- تأكد من Place ID صحيح
- راجع Execution log في Apps Script

---

## 📊 مراقبة التحديثات

### عرض Log الـ Cron Job
```bash
tail -50 /home/user/kero-dentist/cron/reviews_update.log
```

### التحقق من آخر تحديث
```bash
ls -lh /home/user/kero-dentist/cron/reviews_update.log
```

### تشغيل يدوي (للاختبار)
```bash
php /home/user/kero-dentist/cron/auto_update_reviews.php
```

---

## 📁 الملفات المهمة

```
/admin/import_reviews.php          # صفحة الاستيراد اليدوي
/admin/google_reviews.php          # إدارة التقييمات
/admin/settings.php                 # إعدادات التحديث التلقائي
/cron/auto_update_reviews.php      # سكريبت Cron Job
/cron/reviews_update.log           # سجل التحديثات
```

---

## 💡 نصائح

1. **للاستخدام المرة الأولى**: استخدم Outscraper لجلب كل التقييمات دفعة واحدة
2. **للتحديثات المستمرة**: فعّل Cron Job مع Outscraper API
3. **للتوفير**: استخدم Google Sheets إذا كان عدد التحديثات قليل
4. **النسخ الاحتياطي**: احتفظ بملف JSON من Outscraper

---

## 🎯 النتيجة النهائية

بعد الانتهاء من أي من الخيارات:

✅ جميع الـ 129 تقييم ظاهرة في الموقع
✅ فقط التقييمات 3-5 نجوم معروضة
✅ صور المستخدمين من Google
✅ شارة Google على كل تقييم
✅ تحديث تلقائي كل 3 أيام
✅ زر "قيمنا على Google" في الصفحة الرئيسية

---

## 📞 الدعم

إذا واجهت أي مشكلة:
1. راجع قسم "استكشاف الأخطاء" أعلاه
2. راجع ملف الـ log: `/cron/reviews_update.log`
3. تحقق من الإعدادات في `/admin/settings.php`

---

**تم إعداد هذا الدليل بتاريخ:** <?php echo date('Y-m-d'); ?>
**آخر تحديث:** <?php echo date('Y-m-d H:i:s'); ?>
