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
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
$site_phone = getSetting('site_phone', '+20 123 456 7890');
$site_email = getSetting('site_email', 'info@example.com');
$site_address = getSetting('site_address', 'القاهرة، مصر');

// Page title for header
$page_title = 'عن الدكتور';
$page_description = 'تعرف على فريقنا الطبي المتميز وخبراتنا في مجال طب الأسنان';

// Fetch specialties
try {
    $stmt = $conn->prepare("SELECT * FROM specialties WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
} catch(PDOException $e) {
    $specialties = [];
}

// Statistics
$patients_count = getSetting('patients_count', '5000+');
$years_experience = getSetting('years_experience', '15+');
$success_rate = getSetting('success_rate', '98%');
$awards_count = getSetting('awards_count', '25+');
?>
<?php include 'includes/header.php'; ?>

    <style>
        /* Modern About Page Styles */
        .about-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 120px 0 100px;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,144C672,139,768,149,864,154.7C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.95;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Section */
        .stats-section {
            margin-top: -60px;
            position: relative;
            z-index: 3;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .stat-card {
            background: white;
            padding: 40px 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #666;
            font-weight: 600;
        }

        /* About Content Section */
        .about-content-section {
            padding: 100px 0;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-image-wrapper {
            position: relative;
        }

        .about-main-image {
            width: 100%;
            height: 600px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
            position: relative;
        }

        .about-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-badge {
            position: absolute;
            bottom: 30px;
            left: 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .about-text h2 {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .about-text h3 {
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 2;
            color: #666;
            margin-bottom: 20px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .features-list li {
            display: flex;
            align-items: center;
            padding: 15px 0;
            font-size: 1.1rem;
            color: #444;
        }

        .features-list li i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
        }

        /* Team Section */
        .team-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #666;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
        }

        .team-image {
            width: 100%;
            height: 350px;
            overflow: hidden;
            position: relative;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.1);
        }

        .team-info {
            padding: 30px;
            text-align: center;
        }

        .team-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .team-role {
            color: #667eea;
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .team-bio {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .team-social {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .team-social a {
            width: 40px;
            height: 40px;
            background: #f5f7fa;
            color: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .team-social a:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: scale(1.1);
        }

        /* Specialties Section */
        .specialties-section {
            padding: 100px 0;
        }

        .specialties-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .specialty-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .specialty-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.15);
        }

        .specialty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .specialty-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .specialty-desc {
            color: #666;
            line-height: 1.8;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            text-align: center;
            color: white;
        }

        .cta-section h2 {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 18px 50px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .specialties-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .team-grid,
            .specialties-grid {
                grid-template-columns: 1fr;
            }

            .about-text h2 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">من نحن</h1>
                <p class="hero-subtitle">نحن نؤمن بأن الابتسامة الجميلة هي مفتاح الثقة والسعادة. منذ سنوات ونحن نقدم أفضل خدمات طب الأسنان بأحدث التقنيات العالمية</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo htmlspecialchars($patients_count); ?></div>
                    <div class="stat-label">عميل سعيد</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number"><?php echo htmlspecialchars($years_experience); ?></div>
                    <div class="stat-label">سنوات الخبرة</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="stat-number"><?php echo htmlspecialchars($success_rate); ?></div>
                    <div class="stat-label">نسبة النجاح</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-number"><?php echo htmlspecialchars($awards_count); ?></div>
                    <div class="stat-label">جائزة وتكريم</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content Section -->
    <?php if ($main_doctor): ?>
    <section class="about-content-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image-wrapper">
                    <div class="about-main-image">
                        <?php if (!empty($main_doctor['image'])): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($main_doctor['image']); ?>" alt="<?php echo htmlspecialchars($main_doctor['name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/600x800/667eea/ffffff?text=Doctor" alt="Doctor">
                        <?php endif; ?>
                        <?php if (!empty($main_doctor['years_experience'])): ?>
                        <div class="image-badge">
                            <i class="fas fa-award"></i> <?php echo $main_doctor['years_experience']; ?> سنة خبرة
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="about-text">
                    <h2><?php echo htmlspecialchars($main_doctor['name'] ?? $site_name); ?></h2>
                    <?php if (!empty($main_doctor['title'])): ?>
                    <h3><?php echo htmlspecialchars($main_doctor['title']); ?></h3>
                    <?php elseif (!empty($main_doctor['specialization'])): ?>
                    <h3><?php echo htmlspecialchars($main_doctor['specialization']); ?></h3>
                    <?php endif; ?>

                    <?php if (!empty($main_doctor['bio'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($main_doctor['bio'])); ?></p>
                    <?php endif; ?>

                    <ul class="features-list">
                        <?php if (!empty($main_doctor['specialization'])): ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <span>تخصص في <?php echo htmlspecialchars($main_doctor['specialization']); ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($main_doctor['years_experience'])): ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <span><?php echo $main_doctor['years_experience']; ?> سنوات من الخبرة المتميزة</span>
                        </li>
                        <?php endif; ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <span>استخدام أحدث التقنيات العالمية</span>
                        </li>
                        <li>
                            <i class="fas fa-check"></i>
                            <span>رعاية صحية شاملة ومتكاملة</span>
                        </li>
                    </ul>

                    <a href="index.php#booking" class="cta-button">
                        <i class="fas fa-calendar-check"></i> احجز موعدك الآن
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Specialties Section -->
    <?php if (count($specialties) > 0): ?>
    <section class="specialties-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">تخصصاتنا</span>
                <h2 class="section-title">المجالات التي نتميز فيها</h2>
                <p class="section-subtitle">نقدم خدمات متخصصة في جميع مجالات طب الأسنان</p>
            </div>

            <div class="specialties-grid">
                <?php foreach ($specialties as $specialty): ?>
                <div class="specialty-card">
                    <div class="specialty-icon">
                        <i class="fas <?php echo htmlspecialchars($specialty['icon'] ?? 'fa-tooth'); ?>"></i>
                    </div>
                    <h3 class="specialty-title"><?php echo htmlspecialchars($specialty['title']); ?></h3>
                    <?php if (!empty($specialty['description'])): ?>
                    <p class="specialty-desc"><?php echo htmlspecialchars($specialty['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Team Section -->
    <?php if (count($all_doctors) > 1): ?>
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">فريقنا الطبي</span>
                <h2 class="section-title">تعرف على فريق الأطباء</h2>
                <p class="section-subtitle">نخبة من الأطباء المتخصصين ذوي الخبرة الواسعة</p>
            </div>

            <div class="team-grid">
                <?php foreach ($all_doctors as $doctor): ?>
                <div class="team-card">
                    <div class="team-image">
                        <?php if (!empty($doctor['image'])): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x500/667eea/ffffff?text=<?php echo urlencode($doctor['name']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="team-info">
                        <h3 class="team-name"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <?php if (!empty($doctor['title'])): ?>
                        <p class="team-role"><?php echo htmlspecialchars($doctor['title']); ?></p>
                        <?php elseif (!empty($doctor['specialization'])): ?>
                        <p class="team-role"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($doctor['bio'])): ?>
                        <p class="team-bio"><?php echo htmlspecialchars(substr($doctor['bio'], 0, 120)) . '...'; ?></p>
                        <?php endif; ?>

                        <?php if (!empty($doctor['facebook']) || !empty($doctor['instagram']) || !empty($doctor['twitter'])): ?>
                        <div class="team-social">
                            <?php if (!empty($doctor['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['facebook']); ?>" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($doctor['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['instagram']); ?>" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($doctor['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['twitter']); ?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>جاهز لتحصل على ابتسامة أحلامك؟</h2>
            <p>احجز موعدك الآن واستمتع بخدمة طبية متميزة</p>
            <a href="index.php#booking" class="cta-button">
                <i class="fas fa-calendar-check"></i> احجز الآن
            </a>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
