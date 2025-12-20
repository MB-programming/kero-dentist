<?php
require_once 'includes/config.php';

// Fetch all doctors
$stmt = $conn->query("SELECT * FROM doctors WHERE is_active = 1 ORDER BY display_order ASC, id DESC");
$doctors = $stmt->fetchAll();

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
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
                    <li><a href="services.php">الخدمات</a></li>
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
            <h1 style="font-size: 2.5rem; margin-bottom: 15px;">فريقنا الطبي</h1>
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
