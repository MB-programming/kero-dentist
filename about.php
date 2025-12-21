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
$page_title = 'من نحن';
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
        /* About Hero Section */
        .about-hero {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.95), rgba(6, 214, 160, 0.95));
            padding: 140px 0 100px;
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
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 25px;
            animation: fadeInDown 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.95;
            line-height: 1.8;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        /* Stats Cards */
        .stats-section {
            margin-top: -80px;
            position: relative;
            z-index: 3;
            padding-bottom: 80px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .stat-card {
            background: white;
            padding: 50px 30px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0ea5e9, #10b981);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 70px rgba(14, 165, 233, 0.25);
        }

        .stat-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: white;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 1.15rem;
            color: #666;
            font-weight: 600;
        }

        /* Mission & Vision Section */
        .mission-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
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
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #0ea5e9, #10b981);
        }

        .mission-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 25px;
        }

        .mission-card h3 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .mission-card p {
            font-size: 1.1rem;
            line-height: 2;
            color: #64748b;
        }

        /* Doctor Profile Section */
        .doctor-profile-section {
            padding: 100px 0;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 45% 55%;
            gap: 80px;
            align-items: center;
        }

        .profile-image-wrapper {
            position: relative;
        }

        .profile-image-container {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        }

        .profile-image-container::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            border-radius: 30px;
            z-index: -1;
            opacity: 0.2;
        }

        .profile-image {
            width: 100%;
            height: 650px;
            object-fit: cover;
            display: block;
        }

        .experience-badge {
            position: absolute;
            bottom: 30px;
            left: 30px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            padding: 25px 35px;
            border-radius: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .experience-badge i {
            font-size: 2rem;
        }

        .profile-content h2 {
            font-size: 3rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .profile-content h3 {
            font-size: 1.6rem;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .profile-content p {
            font-size: 1.15rem;
            line-height: 2;
            color: #64748b;
            margin-bottom: 30px;
        }

        .profile-features {
            list-style: none;
            padding: 0;
            margin: 40px 0;
        }

        .profile-features li {
            display: flex;
            align-items: center;
            padding: 18px 0;
            font-size: 1.15rem;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .profile-features li:last-child {
            border-bottom: none;
        }

        .profile-features li i {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            font-size: 1.1rem;
        }

        .profile-btn {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            padding: 20px 50px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
            transition: all 0.3s ease;
        }

        .profile-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(14, 165, 233, 0.4);
        }

        /* Specialties Section */
        .specialties-section {
            padding: 100px 0;
            background: white;
        }

        .section-header {
            text-align: center;
            margin-bottom: 70px;
        }

        .section-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-size: 3.2rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            max-width: 700px;
            margin: 0 auto;
        }

        .specialties-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
        }

        .specialty-card {
            background: white;
            padding: 45px 35px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .specialty-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 0;
        }

        .specialty-card:hover::before {
            opacity: 0.05;
        }

        .specialty-card:hover {
            border-color: #0ea5e9;
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(14, 165, 233, 0.2);
        }

        .specialty-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            position: relative;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .specialty-card:hover .specialty-icon {
            transform: rotateY(360deg);
        }

        .specialty-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .specialty-desc {
            color: #64748b;
            line-height: 1.8;
            font-size: 1.05rem;
            position: relative;
            z-index: 1;
        }

        /* Team Section */
        .team-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .team-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
        }

        .team-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px rgba(14, 165, 233, 0.2);
        }

        .team-image {
            width: 100%;
            height: 380px;
            overflow: hidden;
            position: relative;
        }

        .team-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.15);
        }

        .team-info {
            padding: 35px;
            text-align: center;
        }

        .team-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .team-role {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.15rem;
            margin-bottom: 18px;
            font-weight: 700;
        }

        .team-bio {
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .team-social {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .team-social a {
            width: 45px;
            height: 45px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: #0ea5e9;
        }

        .team-social a:hover {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            transform: translateY(-3px);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
            padding: 100px 0;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            font-size: 3.2rem;
            font-weight: 900;
            margin-bottom: 25px;
        }

        .cta-section p {
            font-size: 1.4rem;
            margin-bottom: 45px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: #0ea5e9;
            padding: 22px 60px;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .cta-button i {
            margin-left: 10px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
                font-size: 2.8rem;
            }

            .hero-subtitle {
                font-size: 1.15rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .profile-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .profile-image {
                height: 450px;
            }

            .mission-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .team-grid,
            .specialties-grid {
                grid-template-columns: 1fr;
            }

            .profile-content h2 {
                font-size: 2.2rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .cta-section h2 {
                font-size: 2.2rem;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">من نحن</h1>
                <p class="hero-subtitle">نحن نؤمن بأن الابتسامة الجميلة هي مفتاح الثقة والسعادة. منذ سنوات ونحن نقدم أفضل خدمات طب الأسنان بأحدث التقنيات العالمية ومعايير الجودة الأوروبية</p>
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

    <!-- Mission & Vision Section -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>رؤيتنا</h3>
                    <p>أن نكون الخيار الأول في مجال طب الأسنان على مستوى المنطقة، ونحقق التميز في تقديم خدمات طبية عالية الجودة تساهم في تحسين صحة الفم والأسنان لمجتمعنا.</p>
                </div>

                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>رسالتنا</h3>
                    <p>تقديم رعاية صحية متكاملة للفم والأسنان بأعلى معايير الجودة العالمية، مع الالتزام بالتطوير المستمر واستخدام أحدث التقنيات لضمان راحة ورضا مرضانا.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Profile Section -->
    <?php if ($main_doctor): ?>
    <section class="doctor-profile-section">
        <div class="container">
            <div class="profile-grid">
                <div class="profile-image-wrapper">
                    <div class="profile-image-container">
                        <?php if (!empty($main_doctor['image'])): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($main_doctor['image']); ?>" alt="<?php echo htmlspecialchars($main_doctor['name']); ?>" class="profile-image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/600x800/0ea5e9/ffffff?text=Doctor" alt="Doctor" class="profile-image">
                        <?php endif; ?>
                        <?php if (!empty($main_doctor['years_experience'])): ?>
                        <div class="experience-badge">
                            <i class="fas fa-award"></i>
                            <span><?php echo $main_doctor['years_experience']; ?> سنة خبرة</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-content">
                    <h2><?php echo htmlspecialchars($main_doctor['name'] ?? $site_name); ?></h2>
                    <?php if (!empty($main_doctor['title'])): ?>
                    <h3><?php echo htmlspecialchars($main_doctor['title']); ?></h3>
                    <?php elseif (!empty($main_doctor['specialization'])): ?>
                    <h3><?php echo htmlspecialchars($main_doctor['specialization']); ?></h3>
                    <?php endif; ?>

                    <?php if (!empty($main_doctor['bio'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($main_doctor['bio'])); ?></p>
                    <?php endif; ?>

                    <ul class="profile-features">
                        <?php if (!empty($main_doctor['specialization'])): ?>
                        <li>
                            <i class="fas fa-stethoscope"></i>
                            <span>تخصص في <?php echo htmlspecialchars($main_doctor['specialization']); ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($main_doctor['years_experience'])): ?>
                        <li>
                            <i class="fas fa-graduation-cap"></i>
                            <span><?php echo $main_doctor['years_experience']; ?> سنوات من الخبرة المتميزة في مجال طب الأسنان</span>
                        </li>
                        <?php endif; ?>
                        <li>
                            <i class="fas fa-tooth"></i>
                            <span>استخدام أحدث التقنيات والمعدات الطبية العالمية</span>
                        </li>
                        <li>
                            <i class="fas fa-certificate"></i>
                            <span>شهادات معتمدة من أفضل الجامعات والمراكز الطبية</span>
                        </li>
                        <li>
                            <i class="fas fa-shield-alt"></i>
                            <span>الالتزام بأعلى معايير السلامة والتعقيم الطبي</span>
                        </li>
                    </ul>

                    <a href="index.php#booking" class="profile-btn">
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
                <p class="section-subtitle">نقدم خدمات متخصصة في جميع مجالات طب الأسنان بأعلى مستويات الجودة</p>
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
                <p class="section-subtitle">نخبة من الأطباء المتخصصين ذوي الخبرة الواسعة في جميع مجالات طب الأسنان</p>
            </div>

            <div class="team-grid">
                <?php foreach ($all_doctors as $doctor): ?>
                <div class="team-card">
                    <div class="team-image">
                        <?php if (!empty($doctor['image'])): ?>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x500/0ea5e9/ffffff?text=<?php echo urlencode($doctor['name']); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
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
                        <p class="team-bio"><?php echo htmlspecialchars(substr($doctor['bio'], 0, 130)) . '...'; ?></p>
                        <?php endif; ?>

                        <?php if (!empty($doctor['facebook']) || !empty($doctor['instagram']) || !empty($doctor['twitter'])): ?>
                        <div class="team-social">
                            <?php if (!empty($doctor['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['facebook']); ?>" target="_blank" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($doctor['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['instagram']); ?>" target="_blank" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($doctor['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($doctor['twitter']); ?>" target="_blank" title="Twitter">
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
        <div class="cta-content">
            <div class="container">
                <h2>جاهز لتحصل على ابتسامة أحلامك؟</h2>
                <p>احجز موعدك الآن واستمتع بخدمة طبية متميزة من فريق محترف</p>
                <a href="index.php#booking" class="cta-button">
                    <i class="fas fa-calendar-check"></i>
                    احجز موعدك الآن
                </a>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
