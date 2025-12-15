<?php
require_once __DIR__ . '/../../includes/config.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get notification counts
$stmt = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$pending_bookings = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
$pending_reviews = $stmt->fetchColumn();

$admin_username = $_SESSION['admin_username'];
$admin_initial = mb_substr($admin_username, 0, 1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'لوحة التحكم'; ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Facebook-Style Admin CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin-facebook.css">

    <!-- Admin Pages Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin-pages.css">
</head>
<body>
    <div class="fb-dashboard">
        <!-- Top Navigation Bar -->
        <nav class="fb-topbar">
            <div class="fb-topbar-right">
                <a href="index.php" class="fb-logo">
                    <i class="fas fa-tooth"></i>
                    <span>إدارة العيادة</span>
                </a>
                <input type="search" class="fb-search" placeholder="البحث في لوحة التحكم...">
            </div>

            <div class="fb-topbar-left">
                <button class="fb-icon-btn" id="notificationsBtn">
                    <i class="fas fa-bell"></i>
                    <?php if ($pending_bookings + $pending_reviews > 0): ?>
                    <span class="badge"><?php echo $pending_bookings + $pending_reviews; ?></span>
                    <?php endif; ?>
                </button>

                <button class="fb-icon-btn" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="fb-user-menu">
                    <div class="fb-user-avatar"><?php echo htmlspecialchars($admin_initial); ?></div>
                    <span class="fb-user-name"><?php echo htmlspecialchars($admin_username); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="fb-sidebar" id="sidebar">
            <div class="fb-sidebar-section">
                <a href="index.php" class="fb-menu-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>

                <a href="bookings.php" class="fb-menu-item <?php echo $current_page === 'bookings' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>الحجوزات</span>
                    <?php if ($pending_bookings > 0): ?>
                    <span class="count"><?php echo $pending_bookings; ?></span>
                    <?php endif; ?>
                </a>

                <a href="reviews.php" class="fb-menu-item <?php echo $current_page === 'reviews' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i>
                    <span>التقييمات</span>
                    <?php if ($pending_reviews > 0): ?>
                    <span class="count"><?php echo $pending_reviews; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="fb-sidebar-section">
                <div class="fb-sidebar-title">المحتوى</div>

                <a href="sliders.php" class="fb-menu-item <?php echo $current_page === 'sliders' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>السلايدر</span>
                </a>

                <a href="services.php" class="fb-menu-item <?php echo $current_page === 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-tooth"></i>
                    <span>الخدمات</span>
                </a>

                <a href="doctors.php" class="fb-menu-item <?php echo $current_page === 'doctors' ? 'active' : ''; ?>">
                    <i class="fas fa-user-md"></i>
                    <span>الدكاترة</span>
                </a>

                <a href="packages.php" class="fb-menu-item <?php echo $current_page === 'packages' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>الباقات</span>
                </a>

                <a href="blog.php" class="fb-menu-item <?php echo $current_page === 'blog' ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>المقالات</span>
                </a>
            </div>

            <div class="fb-sidebar-section">
                <div class="fb-sidebar-title">الإعدادات</div>

                <a href="settings.php" class="fb-menu-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>إعدادات الموقع</span>
                </a>

                <a href="menu.php" class="fb-menu-item <?php echo $current_page === 'menu' ? 'active' : ''; ?>">
                    <i class="fas fa-bars"></i>
                    <span>القوائم</span>
                </a>

                <a href="media.php" class="fb-menu-item <?php echo $current_page === 'media' ? 'active' : ''; ?>">
                    <i class="fas fa-photo-video"></i>
                    <span>المكتبة</span>
                </a>

                <a href="users.php" class="fb-menu-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>المستخدمين</span>
                </a>
            </div>

            <div class="fb-sidebar-section">
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="fb-menu-item">
                    <i class="fas fa-external-link-alt"></i>
                    <span>مشاهدة الموقع</span>
                </a>

                <a href="logout.php" class="fb-menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="fb-main">
