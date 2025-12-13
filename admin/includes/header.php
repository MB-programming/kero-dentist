<?php
require_once __DIR__ . '/../../includes/config.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'لوحة التحكم'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-tooth"></i>
                <h2>لوحة التحكم</h2>
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="../index.php" class="<?php echo $current_page === 'index' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li>
                    <a href="../bookings.php" class="<?php echo $current_page === 'bookings' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>الحجوزات</span>
                    </a>
                </li>
                <li>
                    <a href="../services.php" class="<?php echo $current_page === 'services' ? 'active' : ''; ?>">
                        <i class="fas fa-tooth"></i>
                        <span>الخدمات</span>
                    </a>
                </li>
                <li>
                    <a href="../packages.php" class="<?php echo $current_page === 'packages' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i>
                        <span>الباقات</span>
                    </a>
                </li>
                <li>
                    <a href="../blog.php" class="<?php echo $current_page === 'blog' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>المقالات</span>
                    </a>
                </li>
                <li>
                    <a href="../reviews.php" class="<?php echo $current_page === 'reviews' ? 'active' : ''; ?>">
                        <i class="fas fa-star"></i>
                        <span>التقييمات</span>
                    </a>
                </li>
                <li>
                    <a href="../settings.php" class="<?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>الإعدادات</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../../public/index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>مشاهدة الموقع</span>
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
