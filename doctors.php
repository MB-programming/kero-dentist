<?php
require_once 'includes/config.php';

// Fetch all doctors
$stmt = $conn->query("SELECT * FROM doctors WHERE is_active = 1 ORDER BY display_order ASC, id DESC");
$doctors = $stmt->fetchAll();

// Fetch menu items
try {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'header' ORDER BY display_order ASC");
    $stmt->execute();
    $header_menu = $stmt->fetchAll();
} catch(PDOException $e) {
    $header_menu = [];
}

try {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'footer' ORDER BY display_order ASC");
    $stmt->execute();
    $footer_menu = $stmt->fetchAll();
} catch(PDOException $e) {
    $footer_menu = [];
}

// Get site settings
$site_name = getSetting('site_name', 'عيادة الدكتور');
$site_logo = getSetting('site_logo', '');
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
$site_phone = getSetting('site_phone', '+20 123 456 7890');
$site_email = getSetting('site_email', 'info@example.com');
$site_address = getSetting('site_address', 'القاهرة، مصر');
$social_facebook = getSetting('social_facebook', '');
$social_instagram = getSetting('social_instagram', '');
$social_twitter = getSetting('social_twitter', '');
$social_youtube = getSetting('social_youtube', '');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جميع الأطباء - <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="تعرف على فريقنا الطبي المتخصص">

    <!-- Favicon -->
    <?php
    $site_favicon = getSetting('site_favicon');
    if ($site_favicon && file_exists(UPLOAD_PATH . $site_favicon)):
    ?>
    <link rel="icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <div class="modern-nav-content">
                <a href="index.php" class="modern-logo">
                    <?php if ($site_logo && file_exists(UPLOAD_PATH . $site_logo)): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>">
                    <?php else: ?>
                        <i class="fas fa-tooth"></i>
                        <span><?php echo htmlspecialchars($site_name); ?></span>
                    <?php endif; ?>
                </a>

                <ul class="modern-nav-menu" id="modernNavMenu">
                    <?php if (count($header_menu) > 0): ?>
                        <?php foreach ($header_menu as $item): ?>
                            <li><a href="<?php echo htmlspecialchars($item['url']); ?>" target="<?php echo htmlspecialchars($item['target'] ?? '_self'); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default menu if no items in database -->
                        <li><a href="index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                        <li><a href="services.php"><i class="fas fa-tooth"></i> الخدمات</a></li>
                        <li><a href="index.php#packages"><i class="fas fa-box"></i> الباقات</a></li>
                        <li><a href="index.php#reviews"><i class="fas fa-star"></i> آراء العملاء</a></li>
                        <li><a href="index.php#booking" class="modern-cta-btn"><i class="fas fa-calendar-check"></i> احجز الآن</a></li>
                    <?php endif; ?>
                </ul>

                <div class="modern-nav-actions">
                    <a href="index.php#booking" class="modern-booking-btn">
                        <i class="fas fa-calendar-check"></i>
                        <span>احجز الآن</span>
                    </a>

                    <div class="modern-social-icons">
                        <?php if (!empty($social_facebook)): ?>
                            <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank" rel="noopener" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($social_instagram)): ?>
                            <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank" rel="noopener" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($social_twitter)): ?>
                            <a href="<?php echo htmlspecialchars($social_twitter); ?>" target="_blank" rel="noopener" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($social_youtube)): ?>
                            <a href="<?php echo htmlspecialchars($social_youtube); ?>" target="_blank" rel="noopener" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="modern-hamburger" id="modernHamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header" style="padding: 120px 0 60px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; text-align: center; margin-top: 0;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 15px; font-weight: 700;">فريقنا الطبي</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">نخبة من الأطباء المتخصصين ذوي الخبرة الواسعة</p>
        </div>
    </section>

    <!-- All Doctors -->
    <section class="modern-section" style="background: white; padding: 80px 0;">
        <div class="container">
            <div class="doctors-grid">
                <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-card">
                    <div class="doctor-card-inner">
                        <div class="doctor-image-wrapper">
                            <?php if (!empty($doctor['image'])): ?>
                                <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor['image']); ?>"
                                     alt="<?php echo htmlspecialchars($doctor['name']); ?>"
                                     class="doctor-image">
                            <?php else: ?>
                                <div class="doctor-image doctor-image-placeholder">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            <?php endif; ?>
                            <?php if ($doctor['years_experience'] > 0): ?>
                            <div class="doctor-badge">
                                <i class="fas fa-award"></i>
                                <?php echo $doctor['years_experience']; ?> سنة خبرة
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                            <p class="doctor-title"><?php echo htmlspecialchars($doctor['title']); ?></p>
                            <p class="doctor-specialization">
                                <i class="fas fa-stethoscope"></i>
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </p>
                            <p class="doctor-bio"><?php echo htmlspecialchars($doctor['bio']); ?></p>

                            <?php if ($doctor['phone'] || $doctor['email']): ?>
                            <div class="doctor-contact">
                                <?php if ($doctor['phone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($doctor['phone']); ?>" class="doctor-contact-btn">
                                    <i class="fas fa-phone"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($doctor['email']): ?>
                                <a href="mailto:<?php echo htmlspecialchars($doctor['email']); ?>" class="doctor-contact-btn">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($doctor['facebook'] || $doctor['instagram'] || $doctor['twitter']): ?>
                            <div class="doctor-social">
                                <?php if ($doctor['facebook']): ?>
                                <a href="<?php echo htmlspecialchars($doctor['facebook']); ?>" target="_blank" class="doctor-social-btn">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($doctor['instagram']): ?>
                                <a href="<?php echo htmlspecialchars($doctor['instagram']); ?>" target="_blank" class="doctor-social-btn">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($doctor['twitter']): ?>
                                <a href="<?php echo htmlspecialchars($doctor['twitter']); ?>" target="_blank" class="doctor-social-btn">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($doctors) == 0): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-user-md" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666;">لا يوجد أطباء متاحون حالياً</p>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 50px;">
                <a href="index.php" class="modern-btn modern-btn-primary" style="padding: 14px 40px; font-size: 16px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    <i class="fas fa-arrow-right"></i> العودة إلى الرئيسية
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">
                        <?php if ($site_logo && file_exists(UPLOAD_PATH . $site_logo)): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="height: 40px;">
                        <?php else: ?>
                            <i class="fas fa-tooth"></i> <?php echo htmlspecialchars($site_name); ?>
                        <?php endif; ?>
                    </h3>
                    <p class="footer-text"><?php echo htmlspecialchars($doctor_bio); ?></p>
                    <div class="footer-social">
                        <?php if (!empty($social_facebook)): ?>
                        <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank" rel="noopener" class="social-icon" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social_instagram)): ?>
                        <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank" rel="noopener" class="social-icon" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social_twitter)): ?>
                        <a href="<?php echo htmlspecialchars($social_twitter); ?>" target="_blank" rel="noopener" class="social-icon" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social_youtube)): ?>
                        <a href="<?php echo htmlspecialchars($social_youtube); ?>" target="_blank" rel="noopener" class="social-icon" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">روابط سريعة</h3>
                    <ul class="footer-links">
                        <?php if (count($footer_menu) > 0): ?>
                            <?php foreach ($footer_menu as $item): ?>
                                <li><a href="<?php echo htmlspecialchars($item['url']); ?>" target="<?php echo htmlspecialchars($item['target'] ?? '_self'); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default footer menu -->
                            <li><a href="index.php">الرئيسية</a></li>
                            <li><a href="services.php">الخدمات</a></li>
                            <li><a href="index.php#packages">الباقات</a></li>
                            <li><a href="index.php#reviews">آراء العملاء</a></li>
                            <li><a href="index.php#booking">احجز الآن</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">معلومات الاتصال</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($site_address); ?>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo htmlspecialchars($site_phone); ?>"><?php echo htmlspecialchars($site_phone); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($site_email); ?>"><?php echo htmlspecialchars($site_email); ?></a>
                        </li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">ساعات العمل</h3>
                    <ul class="footer-hours">
                        <li><strong>السبت - الخميس:</strong> 9:00 ص - 9:00 م</li>
                        <li><strong>الجمعة:</strong> مغلق</li>
                    </ul>
                    <a href="index.php#booking" class="modern-btn modern-btn-primary" style="margin-top: 20px;">
                        <i class="fas fa-calendar-check"></i>
                        احجز الآن
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
    // Mobile menu toggle
    document.getElementById('modernHamburger').addEventListener('click', function() {
        document.getElementById('modernNavMenu').classList.toggle('active');
    });
    </script>
</body>
</html>
