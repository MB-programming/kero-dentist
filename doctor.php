<?php
require_once 'includes/config.php';

// Fetch active services
$stmt = $conn->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$services = $stmt->fetchAll();

// Fetch active packages
$stmt = $conn->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$packages = $stmt->fetchAll();

// Fetch approved reviews
$stmt = $conn->prepare("SELECT * FROM reviews WHERE is_approved = 1 AND is_displayed = 1 ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$reviews = $stmt->fetchAll();

// Fetch menu items
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'header' ORDER BY display_order ASC");
$stmt->execute();
$header_menu = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'footer' ORDER BY display_order ASC");
$stmt->execute();
$footer_menu = $stmt->fetchAll();

// Get site settings
$site_name = getSetting('site_name', 'عيادة الدكتور');
$site_logo = getSetting('site_logo', '');
$doctor_name = getSetting('doctor_name', 'د. أحمد محمد');
$doctor_title = getSetting('doctor_title', 'استشاري طب وجراحة الفم والأسنان');
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
$doctor_image = getSetting('doctor_image');
$doctor_years = getSetting('doctor_years_experience', '15');
$doctor_patients = getSetting('doctor_total_patients', '5000');
$doctor_cases = getSetting('doctor_success_cases', '3500');
$doctor_about_full = getSetting('doctor_about_full', 'طبيب أسنان متخصص مع خبرة واسعة في جميع مجالات طب وجراحة الفم والأسنان. حاصل على شهادات دولية ومعتمد من أفضل الجامعات العالمية. يؤمن بأن الابتسامة الجميلة هي مفتاح الثقة بالنفس.');
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
    <title><?php echo htmlspecialchars($doctor_name) . ' - ' . htmlspecialchars($site_name); ?></title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- International Telephone Input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .doctor-hero {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.95), rgba(6, 214, 160, 0.95)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            color: white;
            padding: 6rem 0 4rem;
            position: relative;
            overflow: hidden;
        }

        .doctor-hero-content {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 4rem;
            align-items: center;
        }

        .doctor-image-wrapper {
            position: relative;
        }

        .doctor-main-image {
            width: 100%;
            max-width: 400px;
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 5px solid rgba(255, 255, 255, 0.2);
        }

        .doctor-badge {
            position: absolute;
            bottom: 2rem;
            right: -1rem;
            background: var(--accent-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .doctor-info h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .doctor-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .doctor-highlights {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .highlight-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            backdrop-filter: blur(10px);
        }

        .highlight-number {
            font-size: 2.5rem;
            font-weight: 900;
            display: block;
            margin-bottom: 0.5rem;
        }

        .highlight-label {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .about-section {
            padding: 5rem 0;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .about-content h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .about-text {
            font-size: 1.15rem;
            line-height: 2;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .qualifications-list {
            list-style: none;
        }

        .qualification-item {
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .qualification-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .qualification-text {
            flex: 1;
        }

        .qualification-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .qualification-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .specializations {
            background: var(--gray-50);
            padding: 4rem 0;
        }

        .spec-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .spec-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .spec-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .spec-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 180, 216, 0.3);
        }

        .spec-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .spec-desc {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        .achievements {
            padding: 5rem 0;
        }

        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .achievement-card {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 1rem;
            color: white;
        }

        .achievement-number {
            font-size: 3rem;
            font-weight: 900;
            display: block;
            margin-bottom: 0.5rem;
        }

        .achievement-label {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        @media (max-width: 1024px) {
            .doctor-hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .doctor-main-image {
                margin: 0 auto;
            }

            .section-grid {
                grid-template-columns: 1fr;
            }

            .spec-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .achievement-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .doctor-info h1 {
                font-size: 2.5rem;
            }

            .doctor-highlights {
                grid-template-columns: 1fr;
            }

            .spec-grid {
                grid-template-columns: 1fr;
            }

            .achievement-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <div class="modern-nav-content">
                <a href="doctor.php" class="modern-logo">
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
                        <li><a href="#home" class="active"><i class="fas fa-home"></i> الرئيسية</a></li>
                        <li><a href="#services"><i class="fas fa-tooth"></i> الخدمات</a></li>
                        <li><a href="#packages"><i class="fas fa-box"></i> الباقات</a></li>
                        <li><a href="#reviews"><i class="fas fa-star"></i> آراء العملاء</a></li>
                        <li><a href="#booking" class="modern-cta-btn"><i class="fas fa-calendar-check"></i> احجز الآن</a></li>
                    <?php endif; ?>
                </ul>

                <div class="modern-nav-actions">
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

    <!-- Doctor Hero -->
    <section id="home" class="doctor-hero">
        <div class="container">
            <div class="doctor-hero-content">
                <div class="doctor-image-wrapper">
                    <?php if ($doctor_image && file_exists(UPLOAD_PATH . $doctor_image)): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor_image); ?>"
                             alt="<?php echo htmlspecialchars($doctor_name); ?>"
                             class="doctor-main-image"
                             onerror="this.parentElement.innerHTML='<div class=\'doctor-main-image\' style=\'background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; aspect-ratio: 3/4;\'><i class=\'fas fa-user-md\' style=\'font-size: 8rem; color: rgba(255,255,255,0.3);\'></i></div>'">
                    <?php else: ?>
                        <div class="doctor-main-image" style="background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; aspect-ratio: 3/4;">
                            <i class="fas fa-user-md" style="font-size: 8rem; color: rgba(255,255,255,0.3);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="doctor-badge">
                        <i class="fas fa-certificate"></i>
                        معتمد دولياً
                    </div>
                </div>

                <div class="doctor-info">
                    <h1><?php echo htmlspecialchars($doctor_name); ?></h1>
                    <p class="doctor-title"><?php echo htmlspecialchars($doctor_title); ?></p>
                    <p style="font-size: 1.15rem; line-height: 1.8; opacity: 0.95;">
                        <?php echo nl2br(htmlspecialchars($doctor_bio)); ?>
                    </p>

                    <div class="doctor-highlights">
                        <div class="highlight-item">
                            <span class="highlight-number"><?php echo $doctor_years; ?>+</span>
                            <span class="highlight-label">سنوات خبرة</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-number"><?php echo number_format($doctor_patients); ?>+</span>
                            <span class="highlight-label">مريض سعيد</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-number"><?php echo number_format($doctor_cases); ?>+</span>
                            <span class="highlight-label">حالة ناجحة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-grid">
                <div class="about-content">
                    <h2>من هو الدكتور؟</h2>
                    <div class="about-text">
                        <?php echo nl2br(htmlspecialchars($doctor_about_full)); ?>
                    </div>

                    <a href="#booking" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i>
                        احجز موعد الآن
                    </a>
                </div>

                <div class="qualifications">
                    <ul class="qualifications-list">
                        <li class="qualification-item">
                            <div class="qualification-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="qualification-text">
                                <div class="qualification-title">بكالوريوس طب وجراحة الفم والأسنان</div>
                                <div class="qualification-desc">جامعة القاهرة - تقدير امتياز</div>
                            </div>
                        </li>
                        <li class="qualification-item">
                            <div class="qualification-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="qualification-text">
                                <div class="qualification-title">ماجستير في التركيبات الثابتة</div>
                                <div class="qualification-desc">جامعة عين شمس</div>
                            </div>
                        </li>
                        <li class="qualification-item">
                            <div class="qualification-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="qualification-text">
                                <div class="qualification-title">زمالة الكلية الملكية البريطانية</div>
                                <div class="qualification-desc">Royal College - London</div>
                            </div>
                        </li>
                        <li class="qualification-item">
                            <div class="qualification-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="qualification-text">
                                <div class="qualification-title">عضو الجمعية الأمريكية لطب الأسنان</div>
                                <div class="qualification-desc">American Dental Association</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Specializations -->
    <section id="specializations" class="specializations">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">التخصصات والخبرات</h2>
                <p class="section-description">
                    نقدم مجموعة شاملة من الخدمات المتخصصة في طب الأسنان
                </p>
            </div>

            <div class="spec-grid">
                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-teeth-open"></i>
                    </div>
                    <h3 class="spec-title">زراعة الأسنان</h3>
                    <p class="spec-desc">خبرة متقدمة في زراعة الأسنان باستخدام أحدث التقنيات العالمية</p>
                </div>

                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <h3 class="spec-title">التركيبات الثابتة</h3>
                    <p class="spec-desc">تصميم وتركيب التيجان والجسور بأعلى معايير الجودة</p>
                </div>

                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="spec-title">تجميل الأسنان</h3>
                    <p class="spec-desc">ابتسامة هوليود، التبييض، واللومينير لابتسامة مثالية</p>
                </div>

                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <h3 class="spec-title">علاج الجذور</h3>
                    <p class="spec-desc">علاج عصب الأسنان بدون ألم باستخدام أحدث الأجهزة</p>
                </div>

                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="spec-title">طب أسنان الأطفال</h3>
                    <p class="spec-desc">رعاية متخصصة للأطفال في بيئة مريحة وآمنة</p>
                </div>

                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas fa-teeth"></i>
                    </div>
                    <h3 class="spec-title">تقويم الأسنان</h3>
                    <p class="spec-desc">تقويم تقليدي وشفاف لجميع الأعمار</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements -->
    <section class="achievements">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">الإنجازات والأرقام</h2>
            </div>

            <div class="achievement-grid">
                <div class="achievement-card">
                    <span class="achievement-number"><?php echo $doctor_years; ?>+</span>
                    <span class="achievement-label">سنوات خبرة</span>
                </div>
                <div class="achievement-card" style="background: linear-gradient(135deg, var(--accent-color), #059669);">
                    <span class="achievement-number"><?php echo number_format($doctor_patients); ?>+</span>
                    <span class="achievement-label">مريض سعيد</span>
                </div>
                <div class="achievement-card" style="background: linear-gradient(135deg, var(--warning), #d97706);">
                    <span class="achievement-number"><?php echo number_format($doctor_cases); ?>+</span>
                    <span class="achievement-label">حالة ناجحة</span>
                </div>
                <div class="achievement-card" style="background: linear-gradient(135deg, var(--danger), #b91c1c);">
                    <span class="achievement-number">98%</span>
                    <span class="achievement-label">رضا العملاء</span>
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
                    </div>
                </div>
                <?php endforeach; ?>
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

    <!-- Reviews Section -->
    <section id="reviews" class="modern-section" style="background: linear-gradient(135deg, #f8f9ff 0%, #fff5f7 100%);">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">آراء العملاء</span>
                <h2 class="modern-section-title">ماذا يقول عملاؤنا</h2>
                <p class="modern-section-subtitle">تجارب حقيقية من عملاء سعداء بخدماتنا</p>
            </div>

            <?php if (count($reviews) > 0): ?>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="review-info">
                            <h4 class="review-name"><?php echo htmlspecialchars($review['client_name']); ?></h4>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $review['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                    <div class="review-footer">
                        <i class="fas fa-check-circle"></i>
                        <span>عميل معتمد</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <p>لا توجد تقييمات حالياً</p>
            </div>
            <?php endif; ?>
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
                    <input type="tel" name="client_phone" id="client_phone" class="modern-input" required>
                    <div id="phone_carrier" class="phone-carrier-info" style="display: none;"></div>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">رقم واتساب *</label>
                    <input type="tel" name="client_whatsapp" id="client_whatsapp" class="modern-input" required>
                    <div id="whatsapp_carrier" class="phone-carrier-info" style="display: none;"></div>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">البريد الإلكتروني</label>
                    <input type="email" name="client_email" class="modern-input">
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">العنوان</label>
                    <input type="text" name="client_address" class="modern-input">
                </div>

                <div class="modern-form-row">
                    <div class="modern-form-group">
                        <label class="modern-label">تاريخ الحجز *</label>
                        <input type="date" name="booking_date" class="modern-input" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="modern-form-group">
                        <label class="modern-label">الوقت المفضل *</label>
                        <input type="time" name="booking_time" class="modern-input" required>
                    </div>
                </div>

                <div class="modern-form-group">
                    <label class="modern-label">اختر الباقة *</label>
                    <select name="package_id" id="package_select" class="modern-select" required>
                        <option value="">اختر الباقة</option>
                        <?php foreach ($packages as $package): ?>
                        <option value="<?php echo $package['id']; ?>"
                                data-price="<?php echo $package['price']; ?>"
                                data-description="<?php echo htmlspecialchars($package['description'] ?? ''); ?>"
                                data-features="<?php echo htmlspecialchars($package['features'] ?? ''); ?>"
                                data-duration="<?php echo htmlspecialchars($package['duration'] ?? ''); ?>">
                            <?php echo htmlspecialchars($package['name']); ?> - <?php echo number_format($package['price']); ?> جنيه
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Package Details Preview -->
                <div id="package_preview" class="package-preview" style="display: none;">
                    <div class="package-preview-header">
                        <i class="fas fa-box-open"></i>
                        <h4>تفاصيل الباقة المختارة</h4>
                    </div>
                    <div class="package-preview-content">
                        <div class="package-preview-item">
                            <i class="fas fa-tag"></i>
                            <span class="package-preview-label">السعر:</span>
                            <span id="preview_price" class="package-preview-value"></span>
                        </div>
                        <div class="package-preview-item" id="preview_duration_container" style="display: none;">
                            <i class="fas fa-clock"></i>
                            <span class="package-preview-label">المدة:</span>
                            <span id="preview_duration" class="package-preview-value"></span>
                        </div>
                        <div class="package-preview-item" id="preview_description_container" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <span class="package-preview-label">الوصف:</span>
                            <span id="preview_description" class="package-preview-value"></span>
                        </div>
                        <div class="package-preview-features" id="preview_features_container" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <span class="package-preview-label">المميزات:</span>
                            <ul id="preview_features" class="package-features-list"></ul>
                        </div>
                    </div>
                </div>

                <button type="submit" class="modern-btn modern-btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الحجز
                </button>
            </form>
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
                            <li><a href="#home">الرئيسية</a></li>
                            <li><a href="#services">الخدمات</a></li>
                            <li><a href="#packages">الباقات</a></li>
                            <li><a href="#reviews">آراء العملاء</a></li>
                            <li><a href="#booking">احجز الآن</a></li>
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
                    <a href="#booking" class="modern-btn modern-btn-primary" style="margin-top: 20px;">
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- International Telephone Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>

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

    // International Phone Input Setup
    const phoneInputs = [
        { input: document.querySelector("#client_phone"), carrier: document.querySelector("#phone_carrier") },
        { input: document.querySelector("#client_whatsapp"), carrier: document.querySelector("#whatsapp_carrier") }
    ];

    const egyptianCarriers = {
        '010': { name: 'فودافون', color: '#E60000' },
        '011': { name: 'اتصالات', color: '#00B140' },
        '012': { name: 'أورانج', color: '#FF6200' },
        '015': { name: 'وي', color: '#6B2382' }
    };

    function detectEgyptianCarrier(number) {
        const cleanNumber = number.replace(/[\s\-\(\)]/g, '').replace(/^\+20/, '');
        const prefix = cleanNumber.substring(0, 3);
        return egyptianCarriers[prefix] || null;
    }

    phoneInputs.forEach(({input, carrier}) => {
        if (!input) return;

        const iti = window.intlTelInput(input, {
            initialCountry: "eg",
            preferredCountries: ["eg", "sa", "ae", "kw", "qa"],
            separateDialCode: true,
            autoPlaceholder: "aggressive",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
        });

        input.addEventListener('blur', function() {
            const phoneNumber = iti.getNumber();
            const countryData = iti.getSelectedCountryData();

            if (countryData.iso2 === 'eg') {
                const carrierInfo = detectEgyptianCarrier(phoneNumber);
                if (carrierInfo) {
                    carrier.style.display = 'block';
                    carrier.innerHTML = `<i class="fas fa-mobile-alt"></i> <span style="color: ${carrierInfo.color}; font-weight: 600;">${carrierInfo.name}</span>`;
                } else {
                    carrier.style.display = 'none';
                }
            } else {
                carrier.style.display = 'none';
            }
        });

        input.addEventListener('keyup', function() {
            const phoneNumber = iti.getNumber();
            const countryData = iti.getSelectedCountryData();

            if (countryData.iso2 === 'eg' && phoneNumber.length >= 6) {
                const carrierInfo = detectEgyptianCarrier(phoneNumber);
                if (carrierInfo) {
                    carrier.style.display = 'block';
                    carrier.innerHTML = `<i class="fas fa-mobile-alt"></i> <span style="color: ${carrierInfo.color}; font-weight: 600;">${carrierInfo.name}</span>`;
                } else {
                    carrier.style.display = 'none';
                }
            }
        });
    });

    // Form Validation Functions
    function showError(input, message) {
        const formGroup = input.closest('.modern-form-group');
        let errorDiv = formGroup.querySelector('.error-message');

        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            formGroup.appendChild(errorDiv);
        }

        errorDiv.textContent = message;
        input.classList.add('error');
        input.classList.remove('success');
    }

    function showSuccess(input) {
        const formGroup = input.closest('.modern-form-group');
        const errorDiv = formGroup.querySelector('.error-message');

        if (errorDiv) {
            errorDiv.remove();
        }

        input.classList.remove('error');
        input.classList.add('success');
    }

    function validateName(name) {
        if (!name || name.trim().length < 3) {
            return 'الاسم يجب أن يحتوي على 3 أحرف على الأقل';
        }
        if (!/^[\u0600-\u06FFa-zA-Z\s]+$/.test(name)) {
            return 'الاسم يجب أن يحتوي على حروف فقط';
        }
        return null;
    }

    function validatePhone(phone) {
        if (!phone || phone.trim() === '') {
            return 'رقم الهاتف مطلوب';
        }

        const cleanPhone = phone.replace(/[\s\-\(\)]/g, '');

        if (!/^01[0-2,5]{1}[0-9]{8}$/.test(cleanPhone)) {
            return 'رقم الهاتف غير صحيح (يجب أن يبدأ بـ 01 ويحتوي على 11 رقم)';
        }

        return null;
    }

    function validateEmail(email) {
        if (!email || email.trim() === '') {
            return null;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return 'البريد الإلكتروني غير صحيح';
        }

        return null;
    }

    function validateDate(date) {
        if (!date) {
            return 'تاريخ الحجز مطلوب';
        }

        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            return 'لا يمكن اختيار تاريخ في الماضي';
        }

        const threeMonthsLater = new Date();
        threeMonthsLater.setMonth(threeMonthsLater.getMonth() + 3);

        if (selectedDate > threeMonthsLater) {
            return 'يجب أن يكون التاريخ خلال 3 أشهر من اليوم';
        }

        return null;
    }

    function validatePackage(packageId) {
        if (!packageId || packageId === '') {
            return 'يجب اختيار باقة';
        }
        return null;
    }

    // Package Preview Handler
    const packageSelectEl = document.getElementById('package_select');
    const packagePreview = document.getElementById('package_preview');

    if (packageSelectEl) {
        packageSelectEl.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value) {
                const price = selectedOption.getAttribute('data-price');
                const description = selectedOption.getAttribute('data-description');
                const features = selectedOption.getAttribute('data-features');
                const duration = selectedOption.getAttribute('data-duration');

                packagePreview.style.display = 'block';

                document.getElementById('preview_price').textContent = parseFloat(price).toLocaleString('ar-EG') + ' جنيه';

                if (duration && duration.trim() !== '') {
                    document.getElementById('preview_duration_container').style.display = 'flex';
                    document.getElementById('preview_duration').textContent = duration;
                } else {
                    document.getElementById('preview_duration_container').style.display = 'none';
                }

                if (description && description.trim() !== '') {
                    document.getElementById('preview_description_container').style.display = 'flex';
                    document.getElementById('preview_description').textContent = description;
                } else {
                    document.getElementById('preview_description_container').style.display = 'none';
                }

                if (features && features.trim() !== '') {
                    document.getElementById('preview_features_container').style.display = 'block';
                    const featuresList = document.getElementById('preview_features');
                    featuresList.innerHTML = '';

                    const featuresArray = features.split('\n').filter(f => f.trim() !== '');
                    featuresArray.forEach(feature => {
                        const li = document.createElement('li');
                        li.textContent = feature.trim();
                        featuresList.appendChild(li);
                    });
                } else {
                    document.getElementById('preview_features_container').style.display = 'none';
                }

                setTimeout(() => {
                    packagePreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            } else {
                packagePreview.style.display = 'none';
            }
        });
    }

    // Real-time validation
    const form = document.getElementById('modernBookingForm');
    const nameInput = form.querySelector('input[name="client_name"]');
    const phoneInput = form.querySelector('input[name="client_phone"]');
    const whatsappInput = form.querySelector('input[name="client_whatsapp"]');
    const emailInput = form.querySelector('input[name="client_email"]');
    const dateInput = form.querySelector('input[name="booking_date"]');
    const packageSelect = form.querySelector('select[name="package_id"]');

    nameInput.addEventListener('blur', function() {
        const error = validateName(this.value);
        if (error) {
            showError(this, error);
        } else {
            showSuccess(this);
        }
    });

    phoneInput.addEventListener('blur', function() {
        const error = validatePhone(this.value);
        if (error) {
            showError(this, error);
        } else {
            showSuccess(this);
        }
    });

    whatsappInput.addEventListener('blur', function() {
        const error = validatePhone(this.value);
        if (error) {
            showError(this, error);
        } else {
            showSuccess(this);
        }
    });

    emailInput.addEventListener('blur', function() {
        const error = validateEmail(this.value);
        if (error) {
            showError(this, error);
        } else if (this.value.trim() !== '') {
            showSuccess(this);
        }
    });

    dateInput.addEventListener('change', function() {
        const error = validateDate(this.value);
        if (error) {
            showError(this, error);
        } else {
            showSuccess(this);
        }
    });

    packageSelect.addEventListener('change', function() {
        const error = validatePackage(this.value);
        if (error) {
            showError(this, error);
        } else {
            showSuccess(this);
        }
    });

    // Booking form submission
    document.getElementById('modernBookingForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        let isValid = true;

        const nameError = validateName(nameInput.value);
        if (nameError) {
            showError(nameInput, nameError);
            isValid = false;
        } else {
            showSuccess(nameInput);
        }

        const phoneError = validatePhone(phoneInput.value);
        if (phoneError) {
            showError(phoneInput, phoneError);
            isValid = false;
        } else {
            showSuccess(phoneInput);
        }

        const whatsappError = validatePhone(whatsappInput.value);
        if (whatsappError) {
            showError(whatsappInput, whatsappError);
            isValid = false;
        } else {
            showSuccess(whatsappInput);
        }

        const emailError = validateEmail(emailInput.value);
        if (emailError) {
            showError(emailInput, emailError);
            isValid = false;
        } else if (emailInput.value.trim() !== '') {
            showSuccess(emailInput);
        }

        const dateError = validateDate(dateInput.value);
        if (dateError) {
            showError(dateInput, dateError);
            isValid = false;
        } else {
            showSuccess(dateInput);
        }

        const packageError = validatePackage(packageSelect.value);
        if (packageError) {
            showError(packageSelect, packageError);
            isValid = false;
        } else {
            showSuccess(packageSelect);
        }

        if (!isValid) {
            const firstError = this.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

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
                Swal.fire({
                    icon: 'success',
                    title: 'تم إرسال الحجز بنجاح!',
                    text: data.message,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#0ea5e9',
                    timer: 5000,
                    timerProgressBar: true
                });
                this.reset();
                this.querySelectorAll('.error, .success').forEach(el => {
                    el.classList.remove('error', 'success');
                });
                this.querySelectorAll('.error-message').forEach(el => el.remove());
                if (packagePreview) {
                    packagePreview.style.display = 'none';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'حدث خطأ!',
                    text: data.message,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ef4444'
                });
                if (data.debug) {
                    console.error('Debug:', data.debug);
                }
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الاتصال',
                text: 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#ef4444'
            });
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    </script>
</body>
</html>
