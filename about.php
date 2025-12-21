<?php
require_once 'includes/config.php';

// Fetch main doctor (first active doctor)
$stmt = $conn->prepare("SELECT * FROM doctors WHERE is_active = 1 ORDER BY display_order ASC LIMIT 1");
$stmt->execute();
$main_doctor = $stmt->fetch();

// Fetch all doctors
$stmt = $conn->prepare("SELECT * FROM doctors WHERE is_active = 1 ORDER BY display_order ASC");
$stmt->execute();
$all_doctors = $stmt->fetchAll();

// Fetch menu items
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'header' ORDER BY display_order ASC");
$stmt->execute();
$header_menu = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'footer' ORDER BY display_order ASC");
$stmt->execute();
$footer_menu = $stmt->fetchAll();

// Get all site settings
$site_name = getSetting('site_name', 'عيادة الدكتور');
$site_logo = getSetting('site_logo', '');
$social_facebook = getSetting('social_facebook', '');
$social_twitter = getSetting('social_twitter', '');
$social_instagram = getSetting('social_instagram', '');
$social_youtube = getSetting('social_youtube', '');
$social_linkedin = getSetting('social_linkedin', '');
$contact_phone = getSetting('contact_phone', '');
$contact_email = getSetting('contact_email', '');
$contact_address = getSetting('contact_address', '');
$working_hours = getSetting('working_hours', '');
$site_favicon = getSetting('site_favicon', '');

// Fetch specialties
try {
    $stmt = $conn->prepare("SELECT * FROM specialties WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
} catch(PDOException $e) {
    $specialties = [];
}

// Fetch statistics/achievements
$total_patients = getSetting('total_patients', '5000+');
$years_experience = getSetting('years_experience', '15+');
$success_rate = getSetting('success_rate', '98%');
$awards_count = getSetting('awards_count', '25+');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عن الدكتور - <?php echo htmlspecialchars($site_name); ?></title>

    <?php if ($site_favicon): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">

    <style>
        /* About Page Specific Styles */
        .about-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 140px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,144C672,139,768,149,864,154.7C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .about-hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
        }

        .about-hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .about-hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .about-section {
            padding: 80px 0;
        }

        .about-section.light {
            background: #f8f9fa;
        }

        .doctor-profile-about {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 60px;
            align-items: center;
        }

        .doctor-image-wrapper {
            position: relative;
        }

        .doctor-main-image {
            width: 100%;
            max-width: 450px;
            height: 500px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            position: relative;
        }

        .doctor-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doctor-main-image::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 35px;
            z-index: -1;
        }

        .doctor-info-about h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .doctor-info-about .specialty {
            font-size: 1.3rem;
            color: var(--accent);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .doctor-info-about .bio {
            font-size: 1.1rem;
            line-height: 2;
            color: #4a5568;
            margin-bottom: 30px;
        }

        .credentials-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .credentials-list li {
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 1.05rem;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .credentials-list li i {
            color: var(--primary);
            font-size: 1.3rem;
            min-width: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 60px;
        }

        .stat-card {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .stat-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 600;
        }

        .mission-vision-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 50px;
        }

        .mission-card {
            background: white;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }

        .mission-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .mission-card i {
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 25px;
            opacity: 0.1;
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .mission-card h3 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 20px;
            position: relative;
        }

        .mission-card p {
            font-size: 1.1rem;
            line-height: 1.9;
            color: #4a5568;
            position: relative;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }

        .team-member-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
        }

        .team-member-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .team-member-image {
            width: 100%;
            height: 350px;
            overflow: hidden;
            position: relative;
        }

        .team-member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .team-member-card:hover .team-member-image img {
            transform: scale(1.1);
        }

        .team-member-info {
            padding: 30px;
            text-align: center;
        }

        .team-member-info h3 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .team-member-info .role {
            color: var(--accent);
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .team-member-info .description {
            color: #64748b;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        @media (max-width: 1024px) {
            .doctor-profile-about {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .mission-vision-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .about-hero-title {
                font-size: 2.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .mission-card {
                padding: 35px;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <div class="modern-nav-content">
                <a href="index.php" class="modern-logo">
                    <?php if ($site_logo): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>">
                    <?php else: ?>
                        <i class="fas fa-tooth"></i>
                        <span><?php echo htmlspecialchars($site_name); ?></span>
                    <?php endif; ?>
                </a>

                <ul class="modern-nav-menu" id="modernNavMenu">
                    <li><a href="index.php">الرئيسية</a></li>
                    <li><a href="about.php" class="active">عن الدكتور</a></li>
                    <li><a href="services.php">الخدمات</a></li>
                    <li><a href="doctors.php">الأطباء</a></li>
                    <?php if (count($header_menu) > 0): ?>
                        <?php foreach ($header_menu as $item): ?>
                            <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div class="modern-nav-actions">
                    <a href="index.php#booking" class="modern-booking-btn">
                        <i class="fas fa-calendar-check"></i>
                        احجز الآن
                    </a>

                    <div class="modern-social-icons">
                        <?php if ($social_facebook): ?>
                            <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if ($social_instagram): ?>
                            <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($social_twitter): ?>
                            <a href="<?php echo htmlspecialchars($social_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
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

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-content">
                <h1 class="about-hero-title">عن <?php echo htmlspecialchars($site_name); ?></h1>
                <p class="about-hero-subtitle">نحن نؤمن بأن الابتسامة الجميلة هي مفتاح الثقة والسعادة. منذ سنوات ونحن نقدم أفضل خدمات طب الأسنان بأحدث التقنيات</p>
            </div>
        </div>
    </section>

    <!-- Main Doctor Profile -->
    <?php if ($main_doctor): ?>
    <section class="about-section">
        <div class="container">
            <div class="doctor-profile-about">
                <div class="doctor-image-wrapper">
                    <div class="doctor-main-image">
                        <?php if ($main_doctor['image']): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($main_doctor['image']); ?>" alt="<?php echo htmlspecialchars($main_doctor['name']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-md" style="font-size: 120px; color: white; opacity: 0.3;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="doctor-info-about">
                    <h2><?php echo htmlspecialchars($main_doctor['name']); ?></h2>
                    <?php if (!empty($main_doctor['title'])): ?>
                        <p class="specialty"><?php echo htmlspecialchars($main_doctor['title']); ?></p>
                    <?php elseif (!empty($main_doctor['specialization'])): ?>
                        <p class="specialty"><?php echo htmlspecialchars($main_doctor['specialization']); ?></p>
                    <?php endif; ?>

                    <div class="bio">
                        <?php echo nl2br(htmlspecialchars($main_doctor['bio'])); ?>
                    </div>

                    <ul class="credentials-list">
                        <?php if (!empty($main_doctor['specialization'])): ?>
                            <li><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($main_doctor['specialization']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($main_doctor['years_experience'])): ?>
                            <li><i class="fas fa-briefcase"></i> <?php echo $main_doctor['years_experience']; ?> سنوات خبرة</li>
                        <?php endif; ?>
                        <?php if ($main_doctor['phone']): ?>
                            <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($main_doctor['phone']); ?></li>
                        <?php endif; ?>
                        <?php if ($main_doctor['email']): ?>
                            <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($main_doctor['email']); ?></li>
                        <?php endif; ?>
                    </ul>

                    <a href="index.php#booking" class="btn btn-primary" style="padding: 16px 40px; font-size: 1.1rem;">
                        <i class="fas fa-calendar-check"></i> احجز موعد الآن
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Statistics Section -->
    <section class="about-section light">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">إنجازاتنا</span>
                <h2 class="modern-section-title">أرقام تتحدث عن نفسها</h2>
                <p class="modern-section-subtitle">نفخر بثقة عملائنا وإنجازاتنا على مدار السنوات</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-number"><?php echo htmlspecialchars($total_patients); ?></div>
                    <div class="stat-label">مريض سعيد</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-award"></i>
                    <div class="stat-number"><?php echo htmlspecialchars($years_experience); ?></div>
                    <div class="stat-label">سنوات خبرة</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="stat-number"><?php echo htmlspecialchars($success_rate); ?></div>
                    <div class="stat-label">نسبة النجاح</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-trophy"></i>
                    <div class="stat-number"><?php echo htmlspecialchars($awards_count); ?></div>
                    <div class="stat-label">جائزة وتقدير</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="about-section">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">رؤيتنا ورسالتنا</span>
                <h2 class="modern-section-title">ما نؤمن به</h2>
            </div>

            <div class="mission-vision-grid">
                <div class="mission-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>رسالتنا</h3>
                    <p>نلتزم بتقديم أفضل خدمات طب الأسنان باستخدام أحدث التقنيات والمعدات الطبية، مع التركيز على راحة المريض وتحقيق أفضل النتائج. نسعى لجعل تجربة زيارة طبيب الأسنان مريحة وممتعة لجميع أفراد العائلة.</p>
                </div>

                <div class="mission-card">
                    <i class="fas fa-eye"></i>
                    <h3>رؤيتنا</h3>
                    <p>نطمح لأن نكون الخيار الأول في مجال طب الأسنان في المنطقة، من خلال تقديم خدمات متميزة تجمع بين الجودة العالية والأسعار المناسبة. نهدف إلى نشر الوعي بأهمية صحة الفم والأسنان في المجتمع.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Specialties Section -->
    <?php if (count($specialties) > 0): ?>
    <section class="about-section light">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">تخصصاتنا</span>
                <h2 class="modern-section-title">مجالات خبرتنا</h2>
            </div>

            <div class="spec-grid">
                <?php foreach ($specialties as $specialty): ?>
                <div class="spec-card">
                    <div class="spec-icon">
                        <i class="fas <?php echo htmlspecialchars($specialty['icon']); ?>"></i>
                    </div>
                    <h3 class="spec-title"><?php echo htmlspecialchars($specialty['title']); ?></h3>
                    <p class="spec-desc"><?php echo htmlspecialchars($specialty['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Team Section -->
    <?php if (count($all_doctors) > 1): ?>
    <section class="about-section">
        <div class="container">
            <div class="modern-section-header">
                <span class="modern-section-badge">فريق العمل</span>
                <h2 class="modern-section-title">تعرف على أطبائنا</h2>
                <p class="modern-section-subtitle">فريق من الخبراء المتخصصين في مختلف مجالات طب الأسنان</p>
            </div>

            <div class="team-grid">
                <?php foreach ($all_doctors as $doctor): ?>
                <div class="team-member-card">
                    <div class="team-member-image">
                        <?php if ($doctor['image']): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-md" style="font-size: 80px; color: white; opacity: 0.3;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="team-member-info">
                        <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <?php if (!empty($doctor['title'])): ?>
                            <p class="role"><?php echo htmlspecialchars($doctor['title']); ?></p>
                        <?php elseif (!empty($doctor['specialization'])): ?>
                            <p class="role"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($doctor['bio'])): ?>
                            <p class="description"><?php echo htmlspecialchars(substr($doctor['bio'], 0, 100)) . '...'; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="about-section" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 25px; color: white;">هل أنت مستعد لابتسامة أجمل؟</h2>
            <p style="font-size: 1.3rem; margin-bottom: 40px; opacity: 0.95;">احجز موعدك الآن واحصل على استشارة مجانية</p>
            <a href="index.php#booking" class="btn" style="background: white; color: var(--primary); padding: 18px 50px; font-size: 1.2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-calendar-check"></i> احجز موعد الآن
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <!-- Column 1: Logo & Social -->
                <div class="footer-section">
                    <div class="footer-logo">
                        <?php if ($site_logo): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-width: 180px;">
                        <?php else: ?>
                            <i class="fas fa-tooth"></i>
                            <span><?php echo htmlspecialchars($site_name); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="footer-desc">نقدم أفضل خدمات طب الأسنان بأحدث التقنيات العالمية</p>
                    <div class="footer-social">
                        <?php if ($social_facebook): ?>
                            <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if ($social_twitter): ?>
                            <a href="<?php echo htmlspecialchars($social_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if ($social_instagram): ?>
                            <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($social_youtube): ?>
                            <a href="<?php echo htmlspecialchars($social_youtube); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if ($social_linkedin): ?>
                            <a href="<?php echo htmlspecialchars($social_linkedin); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-section">
                    <h3 class="footer-title">روابط سريعة</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="about.php">عن الدكتور</a></li>
                        <li><a href="services.php">الخدمات</a></li>
                        <li><a href="doctors.php">الأطباء</a></li>
                        <li><a href="index.php#booking">احجز الآن</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact Info -->
                <div class="footer-section">
                    <h3 class="footer-title">تواصل معنا</h3>
                    <ul class="footer-contact">
                        <?php if ($contact_phone): ?>
                            <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contact_phone); ?></li>
                        <?php endif; ?>
                        <?php if ($contact_email): ?>
                            <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contact_email); ?></li>
                        <?php endif; ?>
                        <?php if ($contact_address): ?>
                            <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contact_address); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Column 4: Working Hours -->
                <div class="footer-section">
                    <h3 class="footer-title">ساعات العمل</h3>
                    <?php if ($working_hours): ?>
                        <p class="footer-hours"><?php echo nl2br(htmlspecialchars($working_hours)); ?></p>
                    <?php else: ?>
                        <p class="footer-hours">
                            السبت - الخميس: 9 صباحاً - 9 مساءً<br>
                            الجمعة: مغلق
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.getElementById('modernHamburger');
        const navMenu = document.getElementById('modernNavMenu');

        if (hamburger && navMenu) {
            hamburger.addEventListener('click', function() {
                this.classList.toggle('active');
                navMenu.classList.toggle('active');
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
