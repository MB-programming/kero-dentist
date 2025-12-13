<?php
require_once '../includes/config.php';

// Fetch active services
$stmt = $conn->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$services = $stmt->fetchAll();

// Fetch active packages
$stmt = $conn->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$packages = $stmt->fetchAll();

// Fetch published blog posts (latest 3)
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
$stmt->execute();
$blog_posts = $stmt->fetchAll();

// Fetch approved reviews
$stmt = $conn->prepare("SELECT * FROM reviews WHERE is_approved = 1 AND is_displayed = 1 ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$reviews = $stmt->fetchAll();

// Get site settings
$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
$doctor_name = getSetting('doctor_name', 'Dr. Ahmed Mohamed');
$doctor_title = getSetting('doctor_title', 'Dental Specialist');
$doctor_bio = getSetting('doctor_bio', 'Experienced dental specialist');
$hero_title = getSetting('hero_title', 'Your Smile, Our Priority');
$hero_subtitle = getSetting('hero_subtitle', 'Professional dental care');
$site_phone = getSetting('site_phone', '+20 123 456 7890');
$site_email = getSetting('site_email', 'info@example.com');
$site_address = getSetting('site_address', 'Cairo, Egypt');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_name); ?> - <?php echo htmlspecialchars($doctor_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($doctor_bio); ?>">
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
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#about">عن الدكتور</a></li>
                <li><a href="services.php">الخدمات</a></li>
                <li><a href="packages.php">الباقات</a></li>
                <li><a href="blog.php">المقالات</a></li>
                <li><a href="#reviews">آراء العملاء</a></li>
                <li><a href="#booking" class="btn-nav">احجز الآن</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="hero-title"><?php echo htmlspecialchars($hero_title); ?></h1>
            <p class="hero-subtitle"><?php echo htmlspecialchars($hero_subtitle); ?></p>
            <div class="hero-buttons">
                <a href="#booking" class="btn btn-primary">احجز موعد</a>
                <a href="#services" class="btn btn-outline">تعرف على خدماتنا</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="images/doctor-placeholder.jpg" alt="<?php echo htmlspecialchars($doctor_name); ?>">
                </div>
                <div class="about-text">
                    <h2><?php echo htmlspecialchars($doctor_name); ?></h2>
                    <h3><?php echo htmlspecialchars($doctor_title); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($doctor_bio)); ?></p>
                    <div class="about-stats">
                        <div class="stat">
                            <i class="fas fa-award"></i>
                            <h4>+10 سنوات</h4>
                            <p>من الخبرة</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            <h4>+1000</h4>
                            <p>عميل سعيد</p>
                        </div>
                        <div class="stat">
                            <i class="fas fa-smile"></i>
                            <h4>100%</h4>
                            <p>رضا العملاء</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2>خدماتنا</h2>
                <p>نقدم مجموعة متكاملة من خدمات طب الأسنان</p>
            </div>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas <?php echo htmlspecialchars($service['icon'] ?: 'fa-tooth'); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="packages">
        <div class="container">
            <div class="section-header">
                <h2>باقاتنا</h2>
                <p>اختر الباقة المناسبة لك</p>
            </div>
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
                    <a href="#booking" class="btn btn-primary btn-block" onclick="selectPackage(<?php echo $package['id']; ?>, '<?php echo htmlspecialchars($package['name'], ENT_QUOTES); ?>')">احجز الآن</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <?php if (count($blog_posts) > 0): ?>
    <section id="blog" class="blog">
        <div class="container">
            <div class="section-header">
                <h2>مقالاتنا</h2>
                <p>آخر المقالات والنصائح الطبية</p>
            </div>
            <div class="blog-grid">
                <?php foreach ($blog_posts as $post): ?>
                <div class="blog-card">
                    <?php if ($post['featured_image']): ?>
                    <div class="blog-image">
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($post['published_at']); ?></span>
                            <?php if ($post['author']): ?>
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center" style="margin-top: 30px;">
                <a href="blog.php" class="btn btn-outline">عرض جميع المقالات</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Reviews Section -->
    <?php if (count($reviews) > 0): ?>
    <section id="reviews" class="reviews">
        <div class="container">
            <div class="section-header">
                <h2>آراء عملائنا</h2>
                <p>ماذا يقول عملاؤنا عنا</p>
            </div>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                    <div class="review-author">
                        <strong><?php echo htmlspecialchars($review['client_name']); ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Booking Section -->
    <section id="booking" class="booking">
        <div class="container">
            <div class="section-header">
                <h2>احجز موعدك الآن</h2>
                <p>املأ البيانات وسنتواصل معك قريباً</p>
            </div>
            <div class="booking-form-container">
                <form id="bookingForm" class="booking-form" method="POST" action="api/submit-booking.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_name">الاسم الكامل *</label>
                            <input type="text" id="client_name" name="client_name" required>
                        </div>
                        <div class="form-group">
                            <label for="client_phone">رقم الهاتف *</label>
                            <input type="tel" id="client_phone" name="client_phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_whatsapp">رقم الواتساب *</label>
                            <input type="tel" id="client_whatsapp" name="client_whatsapp" required>
                        </div>
                        <div class="form-group">
                            <label for="booking_date">تاريخ الحجز *</label>
                            <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="client_address">العنوان</label>
                        <input type="text" id="client_address" name="client_address">
                    </div>
                    <div class="form-group">
                        <label for="package_id">اختر الباقة *</label>
                        <select id="package_id" name="package_id" required>
                            <option value="">-- اختر الباقة --</option>
                            <?php foreach ($packages as $package): ?>
                            <option value="<?php echo $package['id']; ?>" data-price="<?php echo $package['price']; ?>">
                                <?php echo htmlspecialchars($package['name']); ?> - <?php echo number_format($package['price'], 0); ?> جنيه
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">ملاحظات إضافية</label>
                        <textarea id="notes" name="notes" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-paper-plane"></i> إرسال الحجز
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact">
        <div class="container">
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>الهاتف</h4>
                        <p><?php echo htmlspecialchars($site_phone); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>البريد الإلكتروني</h4>
                        <p><?php echo htmlspecialchars($site_email); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>العنوان</h4>
                        <p><?php echo htmlspecialchars($site_address); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars($doctor_bio); ?></p>
                </div>
                <div class="footer-links">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="#home">الرئيسية</a></li>
                        <li><a href="#about">عن الدكتور</a></li>
                        <li><a href="#services">الخدمات</a></li>
                        <li><a href="#packages">الباقات</a></li>
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
