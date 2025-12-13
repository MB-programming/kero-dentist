<?php
require_once '../includes/config.php';

// Fetch all active packages
$stmt = $conn->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$packages = $stmt->fetchAll();

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>باقاتنا - <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="اختر الباقة المناسبة لك من باقاتنا المتنوعة بأفضل الأسعار">
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
            <h1 style="font-size: 2.5rem; margin-bottom: 15px;">باقاتنا الطبية</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">اختر الباقة المناسبة لك ولعائلتك بأفضل الأسعار</p>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages" style="padding: 80px 20px; background: var(--bg-white);">
        <div class="container">
            <?php if (count($packages) > 0): ?>
                <div class="packages-grid">
                    <?php foreach ($packages as $package): ?>
                    <div class="package-card <?php echo $package['is_popular'] ? 'popular' : ''; ?>">
                        <?php if ($package['is_popular']): ?>
                        <div class="popular-badge">الأكثر طلباً</div>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                        <div class="package-price">
                            <span class="price"><?php echo number_format($package['price'], 0); ?></span>
                            <span class="currency">جنيه</span>
                        </div>
                        <?php if ($package['duration']): ?>
                        <p class="duration">
                            <i class="fas fa-clock"></i> <?php echo htmlspecialchars($package['duration']); ?>
                        </p>
                        <?php endif; ?>
                        <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
                        <?php if ($package['features']): ?>
                        <ul class="package-features">
                            <?php foreach (explode("\n", $package['features']) as $feature): ?>
                                <?php if (trim($feature)): ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <a href="index.php#booking" class="btn btn-primary btn-block" onclick="selectPackage(<?php echo $package['id']; ?>, '<?php echo htmlspecialchars($package['name'], ENT_QUOTES); ?>')">احجز الآن</a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Why Choose Us -->
                <div style="margin-top: 80px; background: var(--bg-light); padding: 60px 20px; border-radius: 15px;">
                    <h2 style="text-align: center; font-size: 2rem; margin-bottom: 40px; color: var(--text-color);">لماذا تختار باقاتنا؟</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-dollar-sign" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h3 style="margin-bottom: 10px;">أسعار مناسبة</h3>
                            <p style="color: var(--text-light);">أسعار تنافسية وباقات متنوعة تناسب الجميع</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--success), #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-award" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h3 style="margin-bottom: 10px;">جودة عالية</h3>
                            <p style="color: var(--text-light);">خدمات طبية متميزة بأعلى معايير الجودة</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--warning), #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-calendar-check" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h3 style="margin-bottom: 10px;">مرونة في المواعيد</h3>
                            <p style="color: var(--text-light);">مواعيد مرنة تناسب جدولك اليومي</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div style="text-align: center; margin-top: 60px;">
                    <h2 style="font-size: 2rem; margin-bottom: 20px; color: var(--text-color);">هل أنت مستعد لتحسين صحة أسنانك؟</h2>
                    <p style="font-size: 1.1rem; color: var(--text-light); margin-bottom: 30px;">احجز موعدك الآن وابدأ رحلتك نحو ابتسامة أجمل</p>
                    <a href="index.php#booking" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-alt"></i> احجز موعدك الآن
                    </a>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-box" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-color); margin-bottom: 10px;">لا توجد باقات متاحة حالياً</h3>
                    <p style="color: var(--text-light);">نعمل على إضافة باقات جديدة قريباً</p>
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
    <script>
    // Function to select package and redirect to booking
    function selectPackage(packageId, packageName) {
        // Redirect to home page booking section with package pre-selected
        window.location.href = 'index.php#booking';
        // Store package ID in session storage to pre-select it
        if (typeof(Storage) !== "undefined") {
            sessionStorage.setItem('selectedPackage', packageId);
        }
    }
    </script>
</body>
</html>
