<?php
$page_title = 'إدارة التقييمات';
include 'includes/header.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE reviews SET is_approved = 1, is_displayed = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = 'تم الموافقة على التقييم';
    } elseif (isset($_POST['reject'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE reviews SET is_approved = 0, is_displayed = 0 WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = 'تم رفض التقييم';
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = 'تم حذف التقييم';
    } elseif (isset($_POST['add_review'])) {
        $client_name = sanitize($_POST['client_name']);
        $rating = intval($_POST['rating']);
        $review_text = sanitize($_POST['review_text']);

        $stmt = $conn->prepare("INSERT INTO reviews (client_name, rating, review_text, is_approved, is_displayed, source) VALUES (?, ?, ?, 1, 1, 'manual')");
        $stmt->execute([$client_name, $rating, $review_text]);
        $success_message = 'تم إضافة التقييم بنجاح';
    }
}

// Fetch reviews
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';
$where = '';
if ($filter === 'pending') {
    $where = 'WHERE is_approved = 0';
} elseif ($filter === 'approved') {
    $where = 'WHERE is_approved = 1';
}

$stmt = $conn->query("SELECT * FROM reviews $where ORDER BY created_at DESC");
$reviews = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة التقييمات</h1>
    <p>الموافقة على التقييمات وإدارتها</p>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?php echo htmlspecialchars($success_message); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>التقييمات</h2>
        <button onclick="openModal('addReviewModal')" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة تقييم يدوياً
        </button>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px;">
            <a href="?filter=all" class="btn btn-sm <?php echo $filter === 'all' ? 'btn-primary' : ''; ?>" style="<?php echo $filter !== 'all' ? 'background: var(--bg-light);' : ''; ?>">الكل</a>
            <a href="?filter=pending" class="btn btn-sm <?php echo $filter === 'pending' ? 'btn-warning' : ''; ?>" style="<?php echo $filter !== 'pending' ? 'background: var(--bg-light);' : ''; ?>">قيد الانتظار</a>
            <a href="?filter=approved" class="btn btn-sm <?php echo $filter === 'approved' ? 'btn-success' : ''; ?>" style="<?php echo $filter !== 'approved' ? 'background: var(--bg-light);' : ''; ?>">معتمد</a>
        </div>

        <?php if (count($reviews) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>التقييم</th>
                        <th>المراجعة</th>
                        <th>المصدر</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo $review['id']; ?></td>
                        <td><?php echo htmlspecialchars($review['client_name']); ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#fbbf24' : '#ddd'; ?>;"></i>
                            <?php endfor; ?>
                        </td>
                        <td style="max-width: 300px;"><?php echo htmlspecialchars($review['review_text']); ?></td>
                        <td>
                            <?php if ($review['source'] === 'email'): ?>
                            <span class="badge badge-info">بريد إلكتروني</span>
                            <?php else: ?>
                            <span class="badge badge-primary">يدوي</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDate($review['created_at']); ?></td>
                        <td>
                            <?php if ($review['is_approved'] && $review['is_displayed']): ?>
                            <span class="badge badge-success">معتمد</span>
                            <?php else: ?>
                            <span class="badge badge-warning">قيد الانتظار</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <?php if (!$review['is_approved']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-sm btn-success" title="موافقة">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="reject" class="btn btn-sm btn-warning" title="رفض">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center" style="padding: 40px; color: var(--text-light);">
            <i class="fas fa-star" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
            لا توجد تقييمات
        </p>
        <?php endif; ?>
    </div>
</div>

<!-- Gmail Integration Info -->
<div class="card">
    <div class="card-header">
        <h2>ربط Gmail API للتقييمات</h2>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            لربط Gmail API لاستقبال التقييمات تلقائياً، اتبع الخطوات في ملف <strong>GMAIL_API_SETUP.md</strong>
        </div>
        <p>البريد الإلكتروني المخصص للتقييمات: <strong><?php echo getSetting('reviews_email'); ?></strong></p>
        <p><a href="settings.php">تحديث الإعدادات</a></p>
    </div>
</div>

<!-- Add Review Modal -->
<div id="addReviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>إضافة تقييم يدوياً</h3>
            <button class="modal-close" onclick="closeModal('addReviewModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <div class="form-group">
                    <label>اسم العميل *</label>
                    <input type="text" name="client_name" required>
                </div>
                <div class="form-group">
                    <label>التقييم *</label>
                    <select name="rating" required>
                        <option value="5">5 نجوم</option>
                        <option value="4">4 نجوم</option>
                        <option value="3">3 نجوم</option>
                        <option value="2">2 نجوم</option>
                        <option value="1">1 نجمة</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>نص المراجعة *</label>
                    <textarea name="review_text" rows="4" required></textarea>
                </div>
                <button type="submit" name="add_review" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> إضافة التقييم
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
