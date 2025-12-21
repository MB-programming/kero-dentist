<?php
require_once '../includes/config.php';

// Check if already migrated
try {
    // Try to update the ENUM
    $sql = "ALTER TABLE reviews MODIFY COLUMN source ENUM('email', 'manual', 'google') DEFAULT 'manual'";
    $pdo->exec($sql);
    echo "✅ تم تحديث جدول reviews بنجاح! الآن يمكن إضافة تقييمات Google.<br>";

    // Add google_review_id column if not exists
    $sql = "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS google_review_id VARCHAR(255) NULL UNIQUE AFTER client_email";
    $pdo->exec($sql);
    echo "✅ تم إضافة حقل google_review_id بنجاح!<br>";

    // Add google_author_photo column if not exists
    $sql = "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS google_author_photo VARCHAR(500) NULL AFTER google_review_id";
    $pdo->exec($sql);
    echo "✅ تم إضافة حقل google_author_photo بنجاح!<br>";

    echo "<br><strong>✅ تم الترحيل بنجاح!</strong><br>";
    echo "<a href='google_reviews.php'>اذهب إلى صفحة إدارة تقييمات Google</a>";

} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage();
}
?>
