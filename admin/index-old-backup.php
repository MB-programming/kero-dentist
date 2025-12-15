<?php
$page_title = 'الرئيسية - لوحة التحكم';
include 'includes/header.php';

// Get stats
$stmt = $conn->query("SELECT COUNT(*) FROM bookings");
$total_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$pending_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = 1");
$published_posts = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
$pending_reviews = $stmt->fetchColumn();

// Get recent bookings
$stmt = $conn->prepare("
    SELECT b.*, p.name as package_name, p.price
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    ORDER BY b.created_at DESC
    LIMIT 5
");
$stmt->execute();
$recent_bookings = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>مرحباً بك في لوحة التحكم</h1>
    <p>نظرة سريعة على الإحصائيات</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_bookings; ?></h3>
            <p>إجمالي الحجوزات</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $pending_bookings; ?></h3>
            <p>حجوزات قيد الانتظار</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $published_posts; ?></h3>
            <p>المقالات المنشورة</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $pending_reviews; ?></h3>
            <p>تقييمات تنتظر الموافقة</p>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="card">
    <div class="card-header">
        <h2>آخر الحجوزات</h2>
        <a href="bookings.php" class="btn btn-primary btn-sm">
            <i class="fas fa-eye"></i> عرض الكل
        </a>
    </div>
    <div class="card-body">
        <?php if (count($recent_bookings) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>الباقة</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['client_phone']); ?></td>
                        <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                        <td><?php echo formatDate($booking['booking_date']); ?></td>
                        <td>
                            <?php
                            $status_badges = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'cancelled' => 'danger',
                                'completed' => 'info'
                            ];
                            $status_labels = [
                                'pending' => 'قيد الانتظار',
                                'confirmed' => 'مؤكد',
                                'cancelled' => 'ملغي',
                                'completed' => 'مكتمل'
                            ];
                            $badge_class = $status_badges[$booking['status']] ?? 'primary';
                            $status_label = $status_labels[$booking['status']] ?? $booking['status'];
                            ?>
                            <span class="badge badge-<?php echo $badge_class; ?>">
                                <?php echo $status_label; ?>
                            </span>
                        </td>
                        <td>
                            <a href="bookings.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center" style="padding: 40px; color: var(--text-light);">
            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
            لا توجد حجوزات حتى الآن
        </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
