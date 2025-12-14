<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    require_once 'includes/config.php';
    $contact_title = getSetting('contact_title', 'تواصل معنا');
    $site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
    echo htmlspecialchars($contact_title) . ' - ' . htmlspecialchars($site_name);
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
                        <li><a href="about.php">من نحن</a></li>
                        <li><a href="contact.php" class="active">تواصل معنا</a></li>
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
            <h1 data-animate="fade-in"><?php echo htmlspecialchars($contact_title); ?></h1>
            <p data-animate="fade-in-up"><?php echo htmlspecialchars(getSetting('contact_description', 'نسعد بتواصلكم معنا')); ?></p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info-cards" data-animate="fade-in-right">
                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>الهاتف</h3>
                        <p><?php echo htmlspecialchars(getSetting('site_phone', '+20 123 456 7890')); ?></p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>البريد الإلكتروني</h3>
                        <p><?php echo htmlspecialchars(getSetting('site_email', 'info@clinic.com')); ?></p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>العنوان</h3>
                        <p><?php echo htmlspecialchars(getSetting('site_address', 'Cairo, Egypt')); ?></p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>مواعيد العمل</h3>
                        <p>السبت - الخميس: 9:00 ص - 9:00 م</p>
                        <p>الجمعة: مغلق</p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-container" data-animate="fade-in-left">
                    <h2>أرسل لنا رسالة</h2>

                    <div id="contactAlert" style="display: none; padding: 15px; margin-bottom: 20px; border-radius: 8px;"></div>

                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <label for="name">الاسم الكامل *</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">الهاتف *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">الموضوع *</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label for="message">الرسالة *</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>

            <!-- Google Maps -->
            <?php
            $map_embed = getSetting('contact_map_embed');
            if ($map_embed): ?>
            <div class="map-container" data-animate="zoom-in">
                <?php echo $map_embed; // Already HTML iframe ?>
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

            // Contact content animations
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
                        transform: ['scale(0.95)', 'scale(1)']
                    },
                    { duration: 0.8, delay: 0.3 }
                );
            });
        });

        // Mobile Menu Toggle
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav').classList.toggle('active');
        });

        // Contact Form Submission
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const alertBox = document.getElementById('contactAlert');

            try {
                const response = await fetch('api/submit-contact.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#d4edda';
                    alertBox.style.color = '#155724';
                    alertBox.style.border = '1px solid #c3e6cb';
                    alertBox.innerHTML = '<i class="fas fa-check-circle"></i> ' + result.message;
                    this.reset();

                    // Animate success message
                    animate(alertBox,
                        { opacity: [0, 1], transform: ['translateY(-10px)', 'translateY(0)'] },
                        { duration: 0.5 }
                    );
                } else {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#f8d7da';
                    alertBox.style.color = '#721c24';
                    alertBox.style.border = '1px solid #f5c6cb';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + result.message;

                    animate(alertBox,
                        { opacity: [0, 1], transform: ['translateY(-10px)', 'translateY(0)'] },
                        { duration: 0.5 }
                    );
                }

                // Hide alert after 5 seconds
                setTimeout(() => {
                    animate(alertBox,
                        { opacity: [1, 0] },
                        { duration: 0.5 }
                    ).finished.then(() => {
                        alertBox.style.display = 'none';
                    });
                }, 5000);

            } catch (error) {
                alertBox.style.display = 'block';
                alertBox.style.background = '#f8d7da';
                alertBox.style.color = '#721c24';
                alertBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ في الإرسال';
            }
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
