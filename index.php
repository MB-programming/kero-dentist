<?php
require_once 'includes/config.php';

// Fetch active sliders
$stmt = $conn->prepare("SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC LIMIT 1");
$stmt->execute();
$hero_slider = $stmt->fetch();

// Fetch active services
$stmt = $conn->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$services = $stmt->fetchAll();

// Fetch active packages
$stmt = $conn->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$packages = $stmt->fetchAll();

// Fetch approved reviews
$stmt = $conn->prepare("SELECT * FROM reviews WHERE is_approved = 1 AND is_displayed = 1 ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$reviews = $stmt->fetchAll();

// Get site settings
$site_name = getSetting('site_name', 'عيادة الدكتور');
$site_logo = getSetting('site_logo', '');
$doctor_name = getSetting('doctor_name', 'د. أحمد محمد');
$doctor_title = getSetting('doctor_title', 'استشاري طب وجراحة الفم والأسنان');
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً');
$hero_title = getSetting('hero_title', 'ابتسامتك المثالية تبدأ هنا');
$hero_subtitle = getSetting('hero_subtitle', 'رعاية أسنان احترافية مع أحدث التقنيات');
$site_phone = getSetting('site_phone', '+20 123 456 7890');
$site_email = getSetting('site_email', 'info@example.com');
$site_address = getSetting('site_address', 'القاهرة، مصر');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_name); ?> - <?php echo htmlspecialchars($doctor_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($doctor_bio); ?>">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Modern CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <a href="index.php" class="modern-logo">
                <?php if ($site_logo && file_exists(UPLOAD_PATH . $site_logo)): ?>
                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>">
                <?php else: ?>
                    <i class="fas fa-tooth"></i>
                    <span><?php echo htmlspecialchars($site_name); ?></span>
                <?php endif; ?>
            </a>

            <ul class="modern-nav-menu" id="modernNavMenu">
                <li><a href="#home" class="active"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li><a href="#about"><i class="fas fa-user-md"></i> من نحن</a></li>
                <li><a href="#services"><i class="fas fa-tooth"></i> الخدمات</a></li>
                <li><a href="#packages"><i class="fas fa-box"></i> الباقات</a></li>
                <li><a href="#reviews"><i class="fas fa-star"></i> آراء العملاء</a></li>
                <li><a href="#booking" class="modern-cta-btn"><i class="fas fa-calendar-check"></i> احجز الآن</a></li>
            </ul>

            <div class="modern-hamburger" id="modernHamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Modern Hero -->
    <section id="home" class="modern-hero">
        <div class="container">
            <div class="modern-hero-content">
                <div class="modern-hero-text">
                    <h1>
                        <?php
                        $title_parts = explode(' ', $hero_title);
                        $last_word = array_pop($title_parts);
                        echo implode(' ', $title_parts) . ' <span>' . $last_word . '</span>';
                        ?>
                    </h1>
                    <p><?php echo htmlspecialchars($hero_subtitle); ?></p>
                    <div class="modern-hero-buttons">
                        <a href="#booking" class="modern-btn modern-btn-primary">
                            <i class="fas fa-calendar-check"></i>
                            احجز موعدك الآن
                        </a>
                        <a href="#services" class="modern-btn modern-btn-secondary">
                            <i class="fas fa-info-circle"></i>
                            اعرف المزيد
                        </a>
                    </div>
                </div>
                <div class="modern-hero-image">
                    <?php if ($hero_slider && !empty($hero_slider['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($hero_slider['image']); ?>" alt="Hero">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?w=800" alt="Dental Care">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="about" class="modern-section" style="background: white;">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">لماذا نحن</span>
                <h2 class="modern-section-title">لماذا تختار عيادتنا؟</h2>
                <p class="modern-section-subtitle">نقدم أفضل خدمات طب الأسنان باستخدام أحدث التقنيات والمعدات</p>
            </div>

            <div class="modern-features-grid">
                <div class="modern-feature-card">
                    <div class="modern-feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="modern-feature-title">خبرة طويلة</h3>
                    <p class="modern-feature-description">أكثر من 15 عاماً من الخبرة في مجال طب الأسنان وعلاج جميع الحالات</p>
                </div>

                <div class="modern-feature-card">
                    <div class="modern-feature-icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <h3 class="modern-feature-title">أحدث التقنيات</h3>
                    <p class="modern-feature-description">نستخدم أحدث الأجهزة والتقنيات في التشخيص والعلاج</p>
                </div>

                <div class="modern-feature-card">
                    <div class="modern-feature-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="modern-feature-title">رعاية شاملة</h3>
                    <p class="modern-feature-description">نوفر جميع خدمات طب الأسنان في مكان واحد</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="modern-section">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">خدماتنا</span>
                <h2 class="modern-section-title">خدمات متكاملة لصحة أسنانك</h2>
                <p class="modern-section-subtitle">نقدم مجموعة واسعة من الخدمات الطبية المتخصصة</p>
            </div>

            <div class="modern-services-grid">
                <?php foreach ($services as $service): ?>
                <div class="modern-service-card">
                    <?php if (!empty($service['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($service['image']); ?>"
                             alt="<?php echo htmlspecialchars($service['title']); ?>"
                             class="modern-service-image">
                    <?php else: ?>
                        <div class="modern-service-image" style="background: linear-gradient(135deg, var(--primary-light), var(--accent)); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tooth" style="font-size: 4rem; color: white;"></i>
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
                        <a href="service-details.php?id=<?php echo $service['id']; ?>" class="modern-service-link">
                            اعرف المزيد
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Doctors Slider Section -->
    <section id="doctors" class="modern-section" style="background: white;">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">فريقنا الطبي</span>
                <h2 class="modern-section-title">تعرف على فريق الدكاترة</h2>
                <p class="modern-section-subtitle">نخبة من الأطباء المتخصصين ذوي الخبرة الواسعة</p>
            </div>

            <div class="doctors-slider-wrapper">
                <div class="doctors-slider" id="doctorsSlider">
                    <?php
                    // Fetch active doctors
                    $stmt = $conn->prepare("SELECT * FROM doctors WHERE is_active = 1 ORDER BY display_order ASC");
                    $stmt->execute();
                    $doctors = $stmt->fetchAll();

                    foreach ($doctors as $doctor):
                    ?>
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

                <!-- Slider Navigation -->
                <button class="slider-nav slider-prev" onclick="slideDoctors(-1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="slider-nav slider-next" onclick="slideDoctors(1)">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <!-- Slider Dots -->
                <div class="slider-dots" id="sliderDots"></div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="modern-section" style="background: white;">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">الباقات</span>
                <h2 class="modern-section-title">باقات علاجية مميزة</h2>
                <p class="modern-section-subtitle">اختر الباقة المناسبة لك</p>
            </div>

            <div class="modern-services-grid">
                <?php foreach ($packages as $package): ?>
                <div class="modern-service-card">
                    <?php if (!empty($package['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($package['image']); ?>"
                             alt="<?php echo htmlspecialchars($package['name']); ?>"
                             class="modern-service-image">
                    <?php endif; ?>
                    <div class="modern-service-content">
                        <h3 class="modern-service-title"><?php echo htmlspecialchars($package['name']); ?></h3>
                        <p class="modern-service-description">
                            <?php
                            $desc = isset($package['short_description']) && !empty($package['short_description'])
                                    ? $package['short_description']
                                    : $package['description'];
                            echo htmlspecialchars($desc);
                            ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                            <span style="font-size: 1.8rem; font-weight: 700; color: var(--primary);">
                                <?php echo number_format($package['price']); ?> جنيه
                            </span>
                            <a href="#booking" class="modern-btn modern-btn-primary" style="padding: 10px 20px; font-size: 14px;">
                                احجز الآن
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="modern-section">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">احجز موعدك</span>
                <h2 class="modern-section-title">احجز موعدك الآن</h2>
                <p class="modern-section-subtitle">املأ النموذج وسنتواصل معك في أقرب وقت</p>
            </div>

            <form class="modern-booking-form" id="modernBookingForm">
                <div class="modern-form-group">
                    <label class="modern-label">الاسم الكامل *</label>
                    <input type="text" name="client_name" class="modern-input" required>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">رقم الهاتف *</label>
                    <input type="tel" name="client_phone" class="modern-input" required>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">رقم واتساب *</label>
                    <input type="tel" name="client_whatsapp" class="modern-input" required>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">البريد الإلكتروني</label>
                    <input type="email" name="client_email" class="modern-input">
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">العنوان</label>
                    <input type="text" name="client_address" class="modern-input">
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">تاريخ الحجز *</label>
                    <input type="date" name="booking_date" class="modern-input" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">اختر الباقة *</label>
                    <select name="package_id" class="modern-select" required>
                        <option value="">اختر الباقة</option>
                        <?php foreach ($packages as $package): ?>
                        <option value="<?php echo $package['id']; ?>">
                            <?php echo htmlspecialchars($package['name']); ?> - <?php echo number_format($package['price']); ?> جنيه
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="modern-btn modern-btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الحجز
                </button>
            </form>
        </div>
    </section>

    <script>
    // Mobile menu toggle
    document.getElementById('modernHamburger').addEventListener('click', function() {
        document.getElementById('modernNavMenu').classList.toggle('active');
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('modernNavMenu').classList.remove('active');
            }
        });
    });

    // Booking form submission
    document.getElementById('modernBookingForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

        try {
            const response = await fetch('api/submit-booking.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                this.reset();
            } else {
                alert(data.message);
                if (data.debug) {
                    console.error('Debug:', data.debug);
                }
            }
        } catch (error) {
            alert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Doctors Slider
    let currentSlide = 0;
    const doctorCards = document.querySelectorAll('.doctor-card');
    const totalDoctors = doctorCards.length;
    const dotsContainer = document.getElementById('sliderDots');

    // Don't init slider if no doctors
    if (totalDoctors > 0) {
        // Create dots
        const totalPages = Math.ceil(totalDoctors / 3);
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('div');
            dot.className = 'slider-dot';
            if (i === 0) dot.classList.add('active');
            dot.onclick = () => goToSlide(i);
            dotsContainer.appendChild(dot);
        }

        function updateSlider() {
            const dots = document.querySelectorAll('.slider-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function goToSlide(index) {
            const totalPages = Math.ceil(totalDoctors / 3);
            currentSlide = Math.max(0, Math.min(index, totalPages - 1));
            updateSlider();
        }

        window.slideDoctors = function(direction) {
            const totalPages = Math.ceil(totalDoctors / 3);
            currentSlide = (currentSlide + direction + totalPages) % totalPages;
            updateSlider();
        };

        // Auto-slide every 5 seconds
        setInterval(() => {
            if (totalDoctors > 3) {
                slideDoctors(1);
            }
        }, 5000);
    }
    </script>
</body>
</html>
