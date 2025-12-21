<?php
$page_title = 'استيراد تقييمات Google بالجملة';
include 'includes/header.php';

// Handle JSON import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_json'])) {
    $json_data = $_POST['json_data'];
    $imported = 0;
    $skipped = 0;
    $errors = [];

    try {
        $reviews_data = json_decode($json_data, true);

        if (!is_array($reviews_data)) {
            throw new Exception('البيانات غير صحيحة. تأكد من تنسيق JSON');
        }

        foreach ($reviews_data as $review) {
            // Validate required fields
            if (empty($review['author_name']) || empty($review['rating'])) {
                $skipped++;
                continue;
            }

            // Only import 3-5 star reviews
            if ($review['rating'] < 3) {
                $skipped++;
                continue;
            }

            // Check if already exists
            $review_id = $review['time'] ?? uniqid();
            $stmt = $pdo->prepare("SELECT id FROM reviews WHERE google_review_id = ?");
            $stmt->execute([$review_id]);

            if ($stmt->fetch()) {
                $skipped++;
                continue;
            }

            // Insert review
            $stmt = $pdo->prepare("
                INSERT INTO reviews (
                    client_name, rating, review_text, source,
                    is_approved, is_displayed, google_review_id, google_author_photo,
                    created_at
                ) VALUES (?, ?, ?, 'google', 1, 1, ?, ?, ?)
            ");

            $text = !empty($review['text']) ? $review['text'] : 'تقييم ممتاز';
            $photo = $review['profile_photo_url'] ?? '';
            $created = isset($review['time']) ? date('Y-m-d H:i:s', $review['time']) : date('Y-m-d H:i:s');

            if ($stmt->execute([
                $review['author_name'],
                $review['rating'],
                $text,
                $review_id,
                $photo,
                $created
            ])) {
                $imported++;
            }
        }

        $success_message = "تم استيراد {$imported} تقييم بنجاح" . ($skipped > 0 ? " (تم تخطي {$skipped})" : "");

    } catch (Exception $e) {
        $error_message = 'خطأ في الاستيراد: ' . $e->getMessage();
    }
}

// Handle CSV import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $imported = 0;
        $skipped = 0;

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($file); // Skip header row

        while (($row = fgetcsv($file)) !== false) {
            // CSV format: name, rating, text, photo_url, date
            if (count($row) < 2) continue;

            $name = $row[0] ?? '';
            $rating = intval($row[1] ?? 0);
            $text = $row[2] ?? 'تقييم ممتاز';
            $photo = $row[3] ?? '';
            $date = $row[4] ?? date('Y-m-d H:i:s');

            if (empty($name) || $rating < 3) {
                $skipped++;
                continue;
            }

            $review_id = md5($name . $text . $date);

            $stmt = $pdo->prepare("SELECT id FROM reviews WHERE google_review_id = ?");
            $stmt->execute([$review_id]);

            if ($stmt->fetch()) {
                $skipped++;
                continue;
            }

            $stmt = $pdo->prepare("
                INSERT INTO reviews (
                    client_name, rating, review_text, source,
                    is_approved, is_displayed, google_review_id, google_author_photo,
                    created_at
                ) VALUES (?, ?, ?, 'google', 1, 1, ?, ?, ?)
            ");

            if ($stmt->execute([$name, $rating, $text, $review_id, $photo, $date])) {
                $imported++;
            }
        }

        fclose($file);
        $success_message = "تم استيراد {$imported} تقييم من CSV بنجاح" . ($skipped > 0 ? " (تم تخطي {$skipped})" : "");
    }
}

// Get current Google reviews count
$stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE source = 'google'");
$google_count = $stmt->fetchColumn();
?>

<div class="page-header">
    <h1><i class="fas fa-file-import"></i> استيراد تقييمات Google بالجملة</h1>
    <p>استيراد جميع التقييمات (129 تقييم) دفعة واحدة</p>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
</div>
<?php endif; ?>

<!-- Current Stats -->
<div class="card" style="margin-bottom: 30px; background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
    <div class="card-body" style="text-align: center; padding: 40px;">
        <h2 style="margin: 0; font-size: 3rem; color: white;"><?php echo $google_count; ?></h2>
        <p style="margin: 10px 0 0; font-size: 1.2rem; opacity: 0.9;">تقييم من Google مستورد حالياً</p>
    </div>
</div>

<!-- Instructions Card -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header" style="background: #f0f9ff;">
        <h2 style="color: #0ea5e9;"><i class="fas fa-info-circle"></i> كيفية جلب الـ 129 تقييم من Google (مجاناً)</h2>
    </div>
    <div class="card-body">
        <h3 style="color: #10b981; margin-bottom: 15px;">✅ الطريقة الأولى: Outscraper (مجاناً 100 تقييم/شهر)</h3>
        <ol style="line-height: 2; margin-bottom: 30px;">
            <li>اذهب إلى: <a href="https://app.outscraper.com/google-maps-reviews-scraper" target="_blank"><strong>Outscraper Reviews Scraper</strong></a></li>
            <li>سجل حساب مجاني (100 request مجاناً)</li>
            <li>الصق رابط عيادتك: <code>https://maps.app.goo.gl/9TCoYUMWk6kHKwqd8</code></li>
            <li>اضبط الإعدادات:
                <ul>
                    <li>Reviews limit: <strong>150</strong> (لضمان جلب الكل)</li>
                    <li>Language: <strong>ar</strong></li>
                    <li>Sort: <strong>newest</strong></li>
                </ul>
            </li>
            <li>اضغط "Start" وانتظر دقيقة</li>
            <li>حمّل النتيجة كـ <strong>JSON</strong></li>
            <li>انسخ محتوى الملف والصقه في المربع بالأسفل ⬇️</li>
        </ol>

        <h3 style="color: #10b981; margin-bottom: 15px;">✅ الطريقة الثانية: Google Sheets Script (مجاناً - غير محدود)</h3>
        <ol style="line-height: 2; margin-bottom: 30px;">
            <li>افتح <a href="https://docs.google.com/spreadsheets/create" target="_blank">Google Sheets جديد</a></li>
            <li>اذهب إلى: <strong>Extensions</strong> > <strong>Apps Script</strong></li>
            <li>انسخ السكريبت من الملف: <code>get_google_reviews.gs</code> (سأوفره لك)</li>
            <li>شغّل السكريبت وسيجلب كل التقييمات تلقائياً</li>
            <li>صدّر الشيت كـ <strong>CSV</strong> وارفعه هنا ⬇️</li>
        </ol>

        <h3 style="color: #10b981; margin-bottom: 15px;">✅ الطريقة الثالثة: Apify (مجاناً محدود)</h3>
        <ol style="line-height: 2;">
            <li>اذهب إلى: <a href="https://apify.com/compass/google-maps-reviews-scraper" target="_blank"><strong>Apify Maps Reviews</strong></a></li>
            <li>سجل حساب مجاني ($5 credit مجاناً)</li>
            <li>أدخل Place ID: <code>CTFqEnDtDuxAEAE</code></li>
            <li>اضغط "Start" وحمّل JSON</li>
        </ol>
    </div>
</div>

<!-- JSON Import -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h2><i class="fab fa-js"></i> استيراد من JSON (موصى به)</h2>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label><strong>الصق بيانات JSON هنا:</strong></label>
                <textarea name="json_data" class="form-control" rows="15" placeholder='[
  {
    "author_name": "أحمد محمد",
    "rating": 5,
    "text": "خدمة ممتازة ونظافة عالية",
    "profile_photo_url": "https://...",
    "time": 1640000000
  },
  ...
]' required style="font-family: monospace; font-size: 0.9rem;"></textarea>
                <small class="form-text">تنسيق JSON من Outscraper أو Apify</small>
            </div>

            <div class="alert alert-info">
                <strong>💡 ملاحظة:</strong> سيتم استيراد فقط التقييمات من 3 إلى 5 نجوم. التقييمات المكررة سيتم تخطيها تلقائياً.
            </div>

            <button type="submit" name="import_json" class="btn btn-success btn-lg">
                <i class="fas fa-file-import"></i> استيراد التقييمات من JSON
            </button>
        </form>
    </div>
</div>

<!-- CSV Import -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h2><i class="fas fa-file-csv"></i> استيراد من CSV</h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label><strong>اختر ملف CSV:</strong></label>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                <small class="form-text">
                    تنسيق CSV المطلوب (مع header):<br>
                    <code>author_name,rating,text,photo_url,date</code>
                </small>
            </div>

            <div class="alert alert-warning">
                <strong>مثال CSV:</strong>
                <pre style="background: #fff; padding: 10px; border-radius: 6px; margin-top: 10px;">author_name,rating,text,photo_url,date
أحمد محمد,5,خدمة ممتازة,https://...,2024-01-15
سارة علي,4,نظيفة جداً,https://...,2024-01-14</pre>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-file-upload"></i> استيراد التقييمات من CSV
            </button>
        </form>
    </div>
</div>

<!-- Auto-Update Settings -->
<div class="card">
    <div class="card-header" style="background: #fef3c7;">
        <h2 style="color: #d97706;"><i class="fas fa-sync-alt"></i> التحديث التلقائي كل 3 أيام</h2>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>📅 لتفعيل التحديث التلقائي:</strong><br>
            سأنشئ لك cron job يعمل كل 3 أيام لجلب التقييمات الجديدة تلقائياً.
        </div>

        <h3 style="margin-top: 30px;">الخيارات:</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            <div style="padding: 20px; background: #f0f9ff; border-radius: 12px; border: 2px solid #0ea5e9;">
                <h4 style="color: #0ea5e9; margin-bottom: 10px;">
                    <i class="fas fa-robot"></i> Cron Job + Outscraper API
                </h4>
                <p>يجلب التقييمات تلقائياً كل 3 أيام من Outscraper API</p>
                <ul>
                    <li>✅ تلقائي 100%</li>
                    <li>✅ 100 request مجاناً/شهر</li>
                    <li>⚠️ يحتاج Outscraper API key</li>
                </ul>
            </div>

            <div style="padding: 20px; background: #f0fdf4; border-radius: 12px; border: 2px solid #10b981;">
                <h4 style="color: #10b981; margin-bottom: 10px;">
                    <i class="fas fa-table"></i> Google Sheets + Cron
                </h4>
                <p>يستورد من Google Sheet كل 3 أيام</p>
                <ul>
                    <li>✅ مجاني 100%</li>
                    <li>✅ غير محدود</li>
                    <li>⚠️ تحتاج تحديث الشيت يدوياً</li>
                </ul>
            </div>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: white; border: 2px dashed #cbd5e0; border-radius: 12px; text-align: center;">
            <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 15px;">اختر الطريقة المناسبة وسأنشئ لك الـ Cron Job</p>
            <a href="settings.php" class="btn btn-primary">
                <i class="fas fa-cog"></i> إعداد التحديث التلقائي
            </a>
        </div>
    </div>
</div>

<style>
pre {
    white-space: pre-wrap;
    word-wrap: break-word;
}

.alert {
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.alert-info {
    background: #e0f2fe;
    border-left: 4px solid #0ea5e9;
    color: #075985;
}

.alert-warning {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    color: #92400e;
}

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
}
</style>

<?php include 'includes/footer.php'; ?>
