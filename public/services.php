<?php
require_once '../includes/config.php';

// Fetch all active services
$stmt = $conn->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$services = $stmt->fetchAll();

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خدماتنا - <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="تعرف على الخدمات الطبية المتميزة التي نقدمها في عيادتنا">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-tooth"></i>
                <span><?php echo htmlspecialchars($site_name); ?></span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="index.php#about">عن الدكتور</a></li>
                <li><a href="services.php">الخدمات</a></li>
                <li><a href="packages.php">الباقات</a></li>
                <li><a href="blog.php">المقالات</a></li>
                <li><a href="index.php#reviews">آراء العملاء</a></li>
                <li><a href="index.php#booking" class="btn-nav">احجز الآن</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <div style="height: 70px;"></div>

    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; padding: 80px 20px; text-align: center;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 15px;">خدماتنا الطبية</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">نقدم مجموعة متكاملة من خدمات طب الأسنان بأحدث التقنيات</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" style="padding: 80px 20px; background: var(--bg-white);">
        <div class="container">
            <?php if (count($services) > 0): ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas <?php echo htmlspecialchars($service['icon'] ?: 'fa-tooth'); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <a href="index.php#booking" class="btn btn-primary" style="margin-top: 20px;">
                            <i class="fas fa-calendar-check"></i> احجز الآن
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- CTA Section -->
                <div style="text-align: center; margin-top: 60px; padding: 60px 20px; background: var(--bg-light); border-radius: 15px;">
                    <h2 style="font-size: 2rem; margin-bottom: 20px; color: var(--text-color);">جاهز لتحسين صحة أسنانك؟</h2>
                    <p style="font-size: 1.1rem; color: var(--text-light); margin-bottom: 30px;">احجز موعدك الآن واحصل على استشارة مجانية</p>
                    <a href="index.php#booking" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-alt"></i> احجز موعدك
                    </a>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-tooth" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-color); margin-bottom: 10px;">لا توجد خدمات متاحة حالياً</h3>
                    <p style="color: var(--text-light);">نعمل على إضافة خدمات جديدة قريباً</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('doctor_bio', '')); ?></p>
                </div>
                <div class="footer-links">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="services.php">الخدمات</a></li>
                        <li><a href="packages.php">الباقات</a></li>
                        <li><a href="blog.php">المقالات</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>تواصل معنا</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
