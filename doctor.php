<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    require_once 'includes/config.php';
    $doctor_name = getSetting('doctor_name', 'الدكتور');
    $site_name = getSetting('site_name', 'عيادة الدكتور');
    echo htmlspecialchars($doctor_name) . ' - ' . htmlspecialchars($site_name);
    ?></title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">

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
    <?php
    // Get doctor information from settings
    $doctor_name = getSetting('doctor_name', 'د. أحمد محمد');
    $doctor_title = getSetting('doctor_title', 'استشاري طب وجراحة الفم والأسنان');
    $doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
    $doctor_image = getSetting('doctor_image');
    $doctor_years = getSetting('doctor_years_experience', '15');
    $doctor_patients = getSetting('doctor_total_patients', '5000');
    $doctor_cases = getSetting('doctor_success_cases', '3500');
    ?>

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-contact">
                        <div class="header-contact-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars(getSetting('site_phone')); ?></span>
                        </div>
                        <div class="header-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars(getSetting('site_email')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="container">
                <nav class="nav-container">
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

                    <ul class="nav-menu">
                        <li><a href="index.php" class="nav-link">الرئيسية</a></li>
                        <li><a href="index.php#services" class="nav-link">الخدمات</a></li>
                        <li><a href="index.php#packages" class="nav-link">الباقات</a></li>
                        <li><a href="doctor.php" class="nav-link active">عن الدكتور</a></li>
                        <li><a href="reviews.php" class="nav-link">آراء العملاء</a></li>
                        <li><a href="contact.php" class="nav-link">تواصل معنا</a></li>
                        <li><a href="index.php#booking" class="nav-cta">احجز الآن</a></li>
                    </ul>

                    <button class="mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Doctor Hero -->
    <section class="doctor-hero">
        <div class="container">
            <div class="doctor-hero-content">
                <div class="doctor-image-wrapper">
                    <?php if ($doctor_image): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor_image); ?>"
                             alt="<?php echo htmlspecialchars($doctor_name); ?>"
                             class="doctor-main-image">
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
                        <?php echo getSetting('doctor_about_full', 'طبيب أسنان متخصص مع خبرة واسعة في جميع مجالات طب وجراحة الفم والأسنان. حاصل على شهادات دولية ومعتمد من أفضل الجامعات العالمية. يؤمن بأن الابتسامة الجميلة هي مفتاح الثقة بالنفس.'); ?>
                    </div>

                    <a href="index.php#booking" class="btn btn-primary">
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
    <section class="specializations">
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
                    <span class="achievement-label">مريض</span>
                </div>
                <div class="achievement-card" style="background: linear-gradient(135deg, var(--warning), #d97706);">
                    <span class="achievement-number"><?php echo number_format($doctor_cases); ?>+</span>
                    <span class="achievement-label">عملية ناجحة</span>
                </div>
                <div class="achievement-card" style="background: linear-gradient(135deg, var(--danger), #b91c1c);">
                    <span class="achievement-number">98%</span>
                    <span class="achievement-label">رضا العملاء</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('doctor_bio')); ?></p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>روابط سريعة</h3>
                    <ul class="footer-links">
                        <li><a href="index.php" class="footer-link">الرئيسية</a></li>
                        <li><a href="doctor.php" class="footer-link">عن الدكتور</a></li>
                        <li><a href="reviews.php" class="footer-link">آراء العملاء</a></li>
                        <li><a href="contact.php" class="footer-link">تواصل معنا</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>الخدمات</h3>
                    <ul class="footer-links">
                        <li><a href="index.php#services" class="footer-link">جميع الخدمات</a></li>
                        <li><a href="index.php#packages" class="footer-link">الباقات</a></li>
                        <li><a href="index.php#booking" class="footer-link">احجز موعد</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>تواصل معنا</h3>
                    <ul class="footer-links">
                        <li class="footer-link">
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars(getSetting('site_phone')); ?>
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars(getSetting('site_email')); ?>
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars(getSetting('site_address')); ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>
</html>
