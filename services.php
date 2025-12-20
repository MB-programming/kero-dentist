<?php
require_once 'includes/config.php';

// Fetch all services
$stmt = $conn->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
$services = $stmt->fetchAll();

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جميع الخدمات - <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="تعرف على جميع خدماتنا الطبية المتخصصة">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-frontend.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="modern-nav" id="navbar">
        <div class="container">
            <div class="modern-nav-wrapper">
                <div class="modern-nav-brand">
                    <i class="fas fa-tooth"></i>
                    <span><?php echo htmlspecialchars($site_name); ?></span>
                </div>

                <ul class="modern-nav-menu" id="navMenu">
                    <li><a href="index.php">الرئيسية</a></li>
                    <li><a href="index.php#about">عن الدكتور</a></li>
                    <li><a href="services.php" class="active">الخدمات</a></li>
                    <li><a href="index.php#packages">الباقات</a></li>
                    <li><a href="blog.php">المقالات</a></li>
                    <li><a href="index.php#reviews">آراء العملاء</a></li>
                </ul>

                <div class="modern-nav-actions">
                    <a href="index.php#booking" class="modern-booking-btn">
                        <i class="fas fa-calendar-check"></i>
                        <span>احجز الآن</span>
                    </a>
                    <div class="modern-hamburger" id="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div style="height: 80px;"></div>

    <!-- Page Header -->
    <section class="page-header" style="padding: 60px 0; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; text-align: center;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 15px;">جميع خدماتنا الطبية</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">تعرف على جميع الخدمات المتخصصة التي نقدمها</p>
        </div>
    </section>

    <!-- All Services -->
    <section class="modern-section" style="background: white; padding: 80px 0;">
        <div class="container">
            <div class="modern-services-grid">
                <?php foreach ($services as $service): ?>
                <div class="modern-service-card">
                    <?php if (!empty($service['image']) && isset($service['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($service['image']); ?>"
                             alt="<?php echo htmlspecialchars($service['title']); ?>"
                             class="modern-service-image">
                    <?php else: ?>
                        <div class="modern-service-image" style="background: linear-gradient(135deg, var(--primary-light), var(--accent)); display: flex; align-items: center; justify-content: center;">
                            <?php
                            $icon = !empty($service['icon']) ? $service['icon'] : 'fa-tooth';
                            ?>
                            <i class="fas <?php echo htmlspecialchars($icon); ?>" style="font-size: 4rem; color: white;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="modern-service-content">
                        <h3 class="modern-service-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p class="modern-service-description">
                            <?php
                            $desc = isset($service['short_description']) && !empty($service['short_description'])
                                    ? $service['short_description']
                                    : $service['description'];
                            echo htmlspecialchars($desc);
                            ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($services) == 0): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666;">لا توجد خدمات متاحة حالياً</p>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 50px;">
                <a href="index.php" class="btn btn-outline-primary" style="padding: 14px 40px; font-size: 16px; border-radius: 8px; text-decoration: none; display: inline-block; border: 2px solid var(--primary); color: var(--primary);">
                    <i class="fas fa-arrow-right"></i> العودة إلى الرئيسية
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="modern-footer-content">
                <div class="modern-footer-brand">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p>عيادة طب الأسنان الرائدة في تقديم أفضل الخدمات الطبية</p>
                </div>
                <div class="modern-footer-links">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="index.php#about">عن الدكتور</a></li>
                        <li><a href="services.php">الخدمات</a></li>
                        <li><a href="blog.php">المقالات</a></li>
                    </ul>
                </div>
                <div class="modern-footer-social">
                    <h4>تواصل معنا</h4>
                    <div class="modern-social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="modern-footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
