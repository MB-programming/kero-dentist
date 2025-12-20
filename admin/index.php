<?php
$page_title = 'لوحة التحكم - الرئيسية';
include 'includes/header.php';

// Get comprehensive stats
$stmt = $conn->query("SELECT COUNT(*) FROM bookings");
$total_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$pending_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'");
$completed_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'approved'");
$approved_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = 1");
$published_posts = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
$pending_reviews = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 1");
$approved_reviews = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM services WHERE is_active = 1");
$active_services = $stmt->fetchColumn();

// Get recent bookings - package_name and package_price are already in bookings table
$stmt = $conn->prepare("
    SELECT b.*
    FROM bookings b
    ORDER BY b.created_at DESC
    LIMIT 10
");
$stmt->execute();
$recent_bookings = $stmt->fetchAll();

// Get today's bookings
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()");
$stmt->execute();
$today_bookings = $stmt->fetchColumn();

// Calculate completion rate
$completion_rate = $total_bookings > 0 ? round(($completed_bookings / $total_bookings) * 100, 1) : 0;

?>

<!-- Page Header -->
<div class="fb-card">
    <div class="fb-card-header">
        <div>
            <h1 class="fb-card-title">مرحباً بك، <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! 👋</h1>
            <p style="color: var(--fb-secondary-text); margin-top: 4px;">إليك ملخص سريع لما يحدث في عيادتك اليوم</p>
        </div>
        <a href="bookings.php" class="fb-btn fb-btn-primary">
            <i class="fas fa-calendar-check"></i>
            عرض جميع الحجوزات
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="fb-stats-grid">
    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $total_bookings; ?></div>
                <div class="fb-stat-label">إجمالي الحجوزات</div>
            </div>
            <div class="fb-stat-icon blue">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="fb-stat-trend">
            <i class="fas fa-arrow-up"></i>
            <span>معدل إتمام <?php echo $completion_rate; ?>%</span>
        </div>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $pending_bookings; ?></div>
                <div class="fb-stat-label">قيد الانتظار</div>
            </div>
            <div class="fb-stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <?php if ($pending_bookings > 0): ?>
        <div class="fb-stat-trend">
            <i class="fas fa-exclamation-circle"></i>
            <span>يحتاج إلى مراجعة</span>
        </div>
        <?php endif; ?>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $completed_bookings; ?></div>
                <div class="fb-stat-label">الحجوزات المكتملة</div>
            </div>
            <div class="fb-stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="fb-stat-trend">
            <i class="fas fa-trophy"></i>
            <span>عمل رائع!</span>
        </div>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $today_bookings; ?></div>
                <div class="fb-stat-label">حجوزات اليوم</div>
            </div>
            <div class="fb-stat-icon blue">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
        <div class="fb-stat-trend">
            <i class="fas fa-calendar"></i>
            <span><?php echo date('Y-m-d'); ?></span>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="fb-stats-grid" style="margin-top: 16px;">
    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $published_posts; ?></div>
                <div class="fb-stat-label">المقالات المنشورة</div>
            </div>
            <div class="fb-stat-icon green">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $approved_reviews; ?></div>
                <div class="fb-stat-label">التقييمات</div>
            </div>
            <div class="fb-stat-icon orange">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <?php if ($pending_reviews > 0): ?>
        <div class="fb-stat-trend">
            <i class="fas fa-clock"></i>
            <span><?php echo $pending_reviews; ?> في الانتظار</span>
        </div>
        <?php endif; ?>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $active_services; ?></div>
                <div class="fb-stat-label">الخدمات النشطة</div>
            </div>
            <div class="fb-stat-icon blue">
                <i class="fas fa-tooth"></i>
            </div>
        </div>
    </div>

    <div class="fb-stat-card">
        <div class="fb-stat-header">
            <div>
                <div class="fb-stat-number"><?php echo $approved_bookings; ?></div>
                <div class="fb-stat-label">حجوزات موافق عليها</div>
            </div>
            <div class="fb-stat-icon green">
                <i class="fas fa-thumbs-up"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="fb-card" style="margin-top: 20px;">
    <div class="fb-card-header">
        <h2 class="fb-card-title">أحدث الحجوزات</h2>
        <a href="bookings.php" class="fb-btn fb-btn-secondary fb-btn-sm">
            <span>عرض الكل</span>
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="fb-card-body no-padding">
        <?php if (count($recent_bookings) > 0): ?>
        <table class="fb-table">
            <thead>
                <tr>
                    <th>اسم العميل</th>
                    <th>رقم الهاتف</th>
                    <th>الباقة</th>
                    <th>تاريخ الحجز</th>
                    <th>اليوم</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_bookings as $booking): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($booking['client_name']); ?></strong>
                    </td>
                    <td>
                        <a href="tel:<?php echo htmlspecialchars($booking['client_phone']); ?>" style="color: var(--fb-blue); text-decoration: none;">
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($booking['client_phone']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($booking['booking_date'])); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_day']); ?></td>
                    <td>
                        <?php
                        $status_class = 'fb-badge-info';
                        $status_text = 'قيد الانتظار';

                        if ($booking['status'] === 'approved') {
                            $status_class = 'fb-badge-success';
                            $status_text = 'موافق عليه';
                        } elseif ($booking['status'] === 'completed') {
                            $status_class = 'fb-badge-success';
                            $status_text = 'مكتمل';
                        } elseif ($booking['status'] === 'rejected') {
                            $status_class = 'fb-badge-danger';
                            $status_text = 'مرفوض';
                        } elseif ($booking['status'] === 'cancelled') {
                            $status_class = 'fb-badge-warning';
                            $status_text = 'ملغي';
                        }
                        ?>
                        <span class="fb-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </td>
                    <td>
                        <a href="bookings.php?id=<?php echo $booking['id']; ?>" class="fb-btn fb-btn-secondary fb-btn-sm">
                            <i class="fas fa-eye"></i>
                            عرض
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="fb-empty-state">
            <div class="fb-empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="fb-empty-title">لا توجد حجوزات بعد</div>
            <div class="fb-empty-text">ستظهر هنا الحجوزات الجديدة من العملاء</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer-facebook.php'; ?>
