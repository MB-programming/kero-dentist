/**
 * Google Apps Script لجلب تقييمات Google Maps
 *
 * الاستخدام:
 * 1. افتح Google Sheets جديد
 * 2. Extensions > Apps Script
 * 3. الصق هذا الكود
 * 4. عدّل PLACE_ID و API_KEY
 * 5. شغّل function: fetchGoogleReviews
 */

// ========== الإعدادات ==========
const PLACE_ID = 'CTFqEnDtDuxAEAE';  // Place ID الخاص بعيادتك
const API_KEY = 'YOUR_GOOGLE_API_KEY_HERE';  // ضع Google API Key هنا (اختياري)

/**
 * طريقة 1: استخدام Google Places API
 * ملاحظة: محدود بـ 5 تقييمات فقط
 */
function fetchGoogleReviewsAPI() {
  if (!API_KEY || API_KEY === 'YOUR_GOOGLE_API_KEY_HERE') {
    SpreadsheetApp.getUi().alert('يرجى إضافة Google API Key أولاً!');
    return;
  }

  const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${PLACE_ID}&fields=name,rating,reviews&key=${API_KEY}&language=ar`;

  try {
    const response = UrlFetchApp.fetch(url);
    const data = JSON.parse(response.getContentText());

    if (data.status === 'OK' && data.result.reviews) {
      const sheet = SpreadsheetApp.getActiveSheet();

      // Clear existing data
      sheet.clear();

      // Add headers
      sheet.appendRow(['اسم المراجع', 'التقييم', 'النص', 'رابط الصورة', 'التاريخ']);

      // Add reviews
      data.result.reviews.forEach(review => {
        if (review.rating >= 3) {  // فقط 3-5 نجوم
          const date = new Date(review.time * 1000);
          sheet.appendRow([
            review.author_name,
            review.rating,
            review.text || 'تقييم ممتاز',
            review.profile_photo_url || '',
            Utilities.formatDate(date, Session.getScriptTimeZone(), 'yyyy-MM-dd HH:mm:ss')
          ]);
        }
      });

      SpreadsheetApp.getUi().alert(`تم استيراد ${data.result.reviews.length} تقييمات بنجاح!`);
    } else {
      SpreadsheetApp.getUi().alert('خطأ: ' + data.status);
    }
  } catch (error) {
    SpreadsheetApp.getUi().alert('خطأ: ' + error.message);
  }
}

/**
 * طريقة 2: استخدام Web Scraping (بدون API)
 * ملاحظة: قد لا يعمل دائماً بسبب قيود Google
 */
function fetchGoogleReviewsScraping() {
  const url = `https://www.google.com/maps/place/?q=place_id:${PLACE_ID}`;

  try {
    const response = UrlFetchApp.fetch(url, {
      muteHttpExceptions: true,
      followRedirects: true
    });

    const html = response.getContentText();

    // محاولة استخراج التقييمات من HTML
    // ملاحظة: هذا قد لا يعمل بشكل موثوق
    SpreadsheetApp.getUi().alert('Web scraping معقد مع Google Maps. يُفضل استخدام Outscraper API بدلاً من ذلك.');

  } catch (error) {
    SpreadsheetApp.getUi().alert('خطأ: ' + error.message);
  }
}

/**
 * طريقة 3: استيراد من JSON (موصى بها)
 * استخدم Outscraper أولاً للحصول على JSON، ثم استورده هنا
 */
function importFromJSON() {
  const ui = SpreadsheetApp.getUi();

  const response = ui.prompt(
    'استيراد من JSON',
    'الصق JSON data من Outscraper هنا:',
    ui.ButtonSet.OK_CANCEL
  );

  if (response.getSelectedButton() === ui.Button.OK) {
    const jsonText = response.getResponseText();

    try {
      const reviews = JSON.parse(jsonText);
      const sheet = SpreadsheetApp.getActiveSheet();

      // Clear existing data
      sheet.clear();

      // Add headers
      sheet.appendRow(['اسم المراجع', 'التقييم', 'النص', 'رابط الصورة', 'التاريخ']);

      // Add reviews
      let count = 0;
      reviews.forEach(review => {
        const rating = review.review_rating || review.rating || 0;
        if (rating >= 3) {
          const timestamp = review.review_timestamp || review.time || Date.now() / 1000;
          const date = new Date(timestamp * 1000);

          sheet.appendRow([
            review.author_title || review.author_name || 'غير معروف',
            rating,
            review.review_text || review.text || 'تقييم ممتاز',
            review.author_image || review.profile_photo_url || '',
            Utilities.formatDate(date, Session.getScriptTimeZone(), 'yyyy-MM-dd HH:mm:ss')
          ]);
          count++;
        }
      });

      ui.alert(`تم استيراد ${count} تقييمات بنجاح!`);

    } catch (error) {
      ui.alert('خطأ في تنسيق JSON: ' + error.message);
    }
  }
}

/**
 * إنشاء قائمة في Sheets
 */
function onOpen() {
  const ui = SpreadsheetApp.getUi();
  ui.createMenu('🌟 تقييمات Google')
    .addItem('📥 استيراد من JSON (موصى به)', 'importFromJSON')
    .addItem('🔄 جلب من Google API (5 تقييمات فقط)', 'fetchGoogleReviewsAPI')
    .addSeparator()
    .addItem('📖 مساعدة', 'showHelp')
    .addToUi();
}

/**
 * عرض تعليمات الاستخدام
 */
function showHelp() {
  const ui = SpreadsheetApp.getUi();
  const helpText = `
📚 دليل الاستخدام:

✅ الطريقة الموصى بها (استيراد من JSON):
1. اذهب إلى: https://app.outscraper.com/google-maps-reviews-scraper
2. أدخل رابط عيادتك على Google Maps
3. اضبط "Reviews Limit" على 150
4. حمّل النتيجة كـ JSON
5. من القائمة أعلاه: استيراد من JSON
6. الصق محتوى الملف

✅ الطريقة البديلة (Google API):
1. احصل على API Key من: https://console.cloud.google.com/
2. فعّل Places API
3. عدّل API_KEY في السكريبت
4. شغّل: جلب من Google API
⚠️ ملاحظة: يجلب فقط 5 تقييمات

📤 التصدير للموقع:
1. File > Download > CSV
2. أو File > Share > Publish to web (اختر CSV)
3. استخدم الرابط في /admin/import_reviews.php
  `;

  ui.alert('مساعدة', helpText, ui.ButtonSet.OK);
}

/**
 * تحديث تلقائي (اختياري)
 * أضف trigger يومي لهذه الدالة من: Edit > Current project's triggers
 */
function autoUpdate() {
  // يمكنك إضافة كود التحديث التلقائي هنا
  // مثلاً: fetchGoogleReviewsAPI() إذا كنت تستخدم API
}
