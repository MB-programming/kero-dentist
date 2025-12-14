<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    require_once 'includes/config.php';
    $about_title = getSetting('about_title', 'من نحن');
    $site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
    echo htmlspecialchars($about_title) . ' - ' . htmlspecialchars($site_name);
    ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Motion.js -->
    <script src="https://cdn.jsdelivr.net/npm/motion@11.7.0/dist/motion.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <?php
                    $site_logo = getSetting('site_logo');
                    if ($site_logo): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>">
                    <?php else: ?>
                        <i class="fas fa-tooth"></i>
                    <?php endif; ?>
                    <span><?php echo htmlspecialchars($site_name); ?></span>
                </div>

                <nav class="nav">
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="index.php#services">الخدمات</a></li>
                        <li><a href="index.php#packages">الباقات</a></li>
                        <li><a href="index.php#blog">المقالات</a></li>
                        <li><a href="about.php" class="active">من نحن</a></li>
                        <li><a href="contact.php">تواصل معنا</a></li>
                    </ul>
                </nav>

                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 data-animate="fade-in"><?php echo htmlspecialchars($about_title); ?></h1>
            <p data-animate="fade-in-up">تعرف علينا أكثر</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-section">
        <div class="container">
            <?php
            $about_image = getSetting('about_image');
            $about_content = getSetting('about_content');

            if ($about_image): ?>
            <div class="about-image" data-animate="fade-in-right">
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($about_image); ?>" alt="<?php echo htmlspecialchars($about_title); ?>">
            </div>
            <?php endif; ?>

            <div class="about-content" data-animate="fade-in-left">
                <?php if ($about_content): ?>
                    <?php echo $about_content; // Already HTML content ?>
                <?php else: ?>
                    <h2>مرحباً بكم</h2>
                    <p>نحن فريق متخصص في طب الأسنان نسعى لتقديم أفضل الخدمات الطبية لعملائنا.</p>
                    <p>يمكنك تعديل هذا المحتوى من لوحة التحكم > الإعدادات > صفحة من نحن</p>
                <?php endif; ?>
            </div>

            <!-- Doctor Info -->
            <?php
            $doctor_name = getSetting('doctor_name');
            $doctor_title = getSetting('doctor_title');
            $doctor_bio = getSetting('doctor_bio');
            $doctor_image = getSetting('doctor_image');

            if ($doctor_name && $doctor_bio): ?>
            <div class="doctor-info" data-animate="zoom-in">
                <div class="doctor-info-inner">
                    <?php if ($doctor_image): ?>
                    <div class="doctor-image">
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor_image); ?>" alt="<?php echo htmlspecialchars($doctor_name); ?>">
                    </div>
                    <?php endif; ?>

                    <div class="doctor-details">
                        <h3><?php echo htmlspecialchars($doctor_name); ?></h3>
                        <?php if ($doctor_title): ?>
                        <p class="doctor-title"><?php echo htmlspecialchars($doctor_title); ?></p>
                        <?php endif; ?>
                        <p class="doctor-bio"><?php echo nl2br(htmlspecialchars($doctor_bio)); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('doctor_bio', 'عيادة أسنان متخصصة')); ?></p>
                </div>

                <div class="footer-section">
                    <h3>روابط سريعة</h3>
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="index.php#services">الخدمات</a></li>
                        <li><a href="index.php#packages">الباقات</a></li>
                        <li><a href="about.php">من نحن</a></li>
                        <li><a href="contact.php">تواصل معنا</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>تواصل معنا</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars(getSetting('site_phone', '+20 123 456 7890')); ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars(getSetting('site_email', 'info@clinic.com')); ?></li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(getSetting('site_address', 'Cairo, Egypt')); ?></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- Motion.js Animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on scroll
            const { inView, animate } = Motion;

            // Page header animations
            inView('[data-animate="fade-in"]', ({ target }) => {
                animate(target,
                    { opacity: [0, 1] },
                    { duration: 0.8, delay: 0.2 }
                );
            });

            inView('[data-animate="fade-in-up"]', ({ target }) => {
                animate(target,
                    {
                        opacity: [0, 1],
                        transform: ['translateY(30px)', 'translateY(0)']
                    },
                    { duration: 0.8, delay: 0.3 }
                );
            });

            // About content animations
            inView('[data-animate="fade-in-right"]', ({ target }) => {
                animate(target,
                    {
                        opacity: [0, 1],
                        transform: ['translateX(50px)', 'translateX(0)']
                    },
                    { duration: 1, delay: 0.2 }
                );
            });

            inView('[data-animate="fade-in-left"]', ({ target }) => {
                animate(target,
                    {
                        opacity: [0, 1],
                        transform: ['translateX(-50px)', 'translateX(0)']
                    },
                    { duration: 1, delay: 0.2 }
                );
            });

            inView('[data-animate="zoom-in"]', ({ target }) => {
                animate(target,
                    {
                        opacity: [0, 1],
                        transform: ['scale(0.8)', 'scale(1)']
                    },
                    { duration: 0.8, delay: 0.3 }
                );
            });
        });

        // Mobile Menu Toggle
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav').classList.toggle('active');
        });
    </script>

    <?php
    // Add Google Analytics if configured
    $ga_id = getSetting('google_analytics_id');
    if ($ga_id): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($ga_id); ?>');
    </script>
    <?php endif; ?>

    <?php
    // Add Facebook Pixel if configured
    $fb_pixel = getSetting('facebook_pixel_id');
    if ($fb_pixel): ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo htmlspecialchars($fb_pixel); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($fb_pixel); ?>&ev=PageView&noscript=1"
    /></noscript>
    <?php endif; ?>
</body>
</html>
