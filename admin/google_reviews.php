<?php
$page_title = 'تقييمات Google';
include 'includes/header.php';

// Get Google settings
$google_place_id = getSetting('google_place_id', '');
$google_api_key = getSetting('google_api_key', '');

// Handle manual add Google review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_manual'])) {
    $client_name = sanitize($_POST['client_name']);
    $rating = intval($_POST['rating']);
    $review_text = sanitize($_POST['review_text']);
    $google_author_photo = sanitize($_POST['google_author_photo'] ?? '');

    if ($rating >= 3 && $rating <= 5) {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (client_name, rating, review_text, source, is_approved, is_displayed, google_author_photo)
            VALUES (?, ?, ?, 'google', 1, 1, ?)
        ");

        if ($stmt->execute([$client_name, $rating, $review_text, $google_author_photo])) {
            $success_message = 'تم إضافة التقييم من Google بنجاح!';
        } else {
            $error_message = 'فشل إضافة التقييم';
        }
    } else {
        $error_message = 'التقييمات المسموح بها من 3 إلى 5 نجوم فقط';
    }
}

// Handle delete review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ? AND source = 'google'");
    if ($stmt->execute([$id])) {
        $success_message = 'تم حذف التقييم بنجاح';
    }
    header('Location: google_reviews.php');
    exit;
}

// Handle fetch from Google API
$fetch_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_api']) && !empty($google_place_id) && !empty($google_api_key)) {
    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$google_place_id}&fields=name,rating,reviews&key={$google_api_key}&language=ar";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['result']['reviews'])) {
        $imported = 0;
        foreach ($data['result']['reviews'] as $review) {
            if ($review['rating'] >= 3) {
                // Check if already exists
                $stmt = $pdo->prepare("SELECT id FROM reviews WHERE google_review_id = ?");
                $stmt->execute([$review['time']]);

                if (!$stmt->fetch()) {
                    $stmt = $pdo->prepare("
                        INSERT INTO reviews (
                            client_name, rating, review_text, source,
                            is_approved, is_displayed, google_review_id, google_author_photo
                        ) VALUES (?, ?, ?, 'google', 1, 1, ?, ?)
                    ");

                    $stmt->execute([
                        $review['author_name'],
                        $review['rating'],
                        $review['text'] ?? 'تقييم ممتاز',
                        $review['time'],
                        $review['profile_photo_url'] ?? ''
                    ]);
                    $imported++;
                }
            }
        }
        $fetch_message = "تم استيراد {$imported} تقييم جديد من Google";
    } else {
        $fetch_message = 'فشل جلب التقييمات. تحقق من Place ID و API Key';
    }
}

// Get all Google reviews
$stmt = $pdo->query("
    SELECT * FROM reviews
    WHERE source = 'google'
    ORDER BY created_at DESC
");
$google_reviews = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="fab fa-google"></i> تقييمات Google Maps</h1>
    <p>إدارة التقييمات المستوردة من Google My Business</p>
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

<?php if ($fetch_message): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> <?php echo $fetch_message; ?>
</div>
<?php endif; ?>

<!-- Google Settings -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h2><i class="fas fa-cog"></i> إعدادات Google</h2>
    </div>
    <div class="card-body">
        <div class="info-box" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #1976d2;"><i class="fas fa-info-circle"></i> روابط التقييم الخاصة بك:</h3>
            <div style="margin: 15px 0;">
                <strong>رابط المراجعة:</strong><br>
                <a href="https://g.page/r/CTFqEnDtDuxAEAE/review" target="_blank" style="color: #2196f3;">
                    https://g.page/r/CTFqEnDtDuxAEAE/review
                </a>
            </div>
            <div style="margin: 15px 0;">
                <strong>رابط الملف التعريفي:</strong><br>
                <a href="https://maps.app.goo.gl/9TCoYUMWk6kHKwqd8" target="_blank" style="color: #2196f3;">
                    https://maps.app.goo.gl/9TCoYUMWk6kHKwqd8
                </a>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: white; border-radius: 6px;">
                <strong>Place ID المستخرج:</strong> <code>CTFqEnDtDuxAEAE</code>
            </div>
        </div>

        <?php if (empty($google_api_key)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            لاستخدام الجلب التلقائي من Google API، يجب تعيين Google API Key في
            <a href="settings.php">إعدادات الموقع</a>
        </div>
        <?php endif; ?>

        <form method="POST" style="display: inline-block; margin-left: 10px;">
            <button type="submit" name="fetch_api" class="btn btn-primary" <?php echo (empty($google_api_key) || empty($google_place_id)) ? 'disabled' : ''; ?>>
                <i class="fas fa-sync-alt"></i> جلب تلقائي من Google API
            </button>
        </form>

        <a href="settings.php" class="btn btn-secondary">
            <i class="fas fa-cog"></i> تعديل إعدادات Google
        </a>
    </div>
</div>

<!-- Manual Add -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h2><i class="fas fa-plus-circle"></i> إضافة تقييم يدوياً من Google</h2>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>كيفية النسخ من Google Maps:</strong><br>
            1. افتح <a href="https://maps.app.goo.gl/9TCoYUMWk6kHKwqd8" target="_blank">صفحة عيادتك على Google Maps</a><br>
            2. اقرأ التقييمات<br>
            3. انسخ اسم المقيّم، النجوم، والتعليق<br>
            4. الصق هنا (فقط التقييمات من 3 إلى 5 نجوم)
        </div>

        <form method="POST">
            <div class="form-group">
                <label>اسم المقيّم</label>
                <input type="text" name="client_name" class="form-control" required placeholder="مثال: أحمد محمد">
            </div>

            <div class="form-group">
                <label>التقييم (نجوم)</label>
                <select name="rating" class="form-control" required>
                    <option value="5">⭐⭐⭐⭐⭐ (5 نجوم)</option>
                    <option value="4">⭐⭐⭐⭐ (4 نجوم)</option>
                    <option value="3">⭐⭐⭐ (3 نجوم)</option>
                </select>
            </div>

            <div class="form-group">
                <label>نص التقييم</label>
                <textarea name="review_text" class="form-control" rows="4" required placeholder="انسخ تعليق المقيّم من Google Maps"></textarea>
            </div>

            <div class="form-group">
                <label>رابط صورة المقيّم (اختياري)</label>
                <input type="url" name="google_author_photo" class="form-control" placeholder="https://...">
                <small class="form-text">اضغط بزر الماوس الأيمن على صورة المقيّم في Google Maps > نسخ رابط الصورة</small>
            </div>

            <button type="submit" name="add_manual" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة التقييم
            </button>
        </form>
    </div>
</div>

<!-- Google Reviews List -->
<div class="card">
    <div class="card-header">
        <h2><i class="fab fa-google"></i> التقييمات المستوردة من Google (<?php echo count($google_reviews); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (count($google_reviews) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>المقيّم</th>
                        <th>التقييم</th>
                        <th>التعليق</th>
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($google_reviews as $review): ?>
                    <tr>
                        <td>
                            <?php if ($review['google_author_photo']): ?>
                                <img src="<?php echo htmlspecialchars($review['google_author_photo']); ?>"
                                     alt="<?php echo htmlspecialchars($review['client_name']); ?>"
                                     style="width: 40px; height: 40px; border-radius: 50%; margin-left: 10px; vertical-align: middle;">
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($review['client_name']); ?></strong>
                        </td>
                        <td>
                            <span style="color: #ffc107; font-size: 1.2rem;">
                                <?php echo str_repeat('⭐', $review['rating']); ?>
                            </span>
                        </td>
                        <td style="max-width: 300px;">
                            <?php echo nl2br(htmlspecialchars(substr($review['review_text'], 0, 100))); ?>
                            <?php if (strlen($review['review_text']) > 100): ?>...<?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $review['is_displayed'] ? 'success' : 'secondary'; ?>">
                                <?php echo $review['is_displayed'] ? 'معروض' : 'مخفي'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></td>
                        <td>
                            <a href="reviews.php?edit=<?php echo $review['id']; ?>" class="btn btn-sm btn-primary" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $review['id']; ?>"
                               onclick="return confirm('هل أنت متأكد من حذف هذا التقييم؟')"
                               class="btn btn-sm btn-danger" title="حذف">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fab fa-google" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3>لا توجد تقييمات من Google</h3>
            <p>استخدم الجلب التلقائي أو أضف التقييمات يدوياً</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.info-box {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.badge-success {
    background: #10b981;
    color: white;
}

.badge-secondary {
    background: #6b7280;
    color: white;
}
</style>

<?php include 'includes/footer.php'; ?>
