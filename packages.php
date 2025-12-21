<?php
require_once 'includes/config.php';

// Fetch all active packages
try {
    $stmt = $conn->prepare("SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC, is_popular DESC");
    $stmt->execute();
    $packages = $stmt->fetchAll();
} catch(PDOException $e) {
    $packages = [];
}

// Get site settings
$site_name = getSetting('site_name', 'عيادة الدكتور');
$site_phone = getSetting('site_phone', '+20 123 456 7890');
$site_email = getSetting('site_email', 'info@example.com');

$site_logo = getSetting('site_logo', '');
$social_facebook = getSetting('social_facebook', '');
$social_instagram = getSetting('social_instagram', '');
$social_twitter = getSetting('social_twitter', '');
$social_youtube = getSetting('social_youtube', '');
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
$site_address = getSetting('site_address', 'القاهرة، مصر');

// Page title for header
$page_title = 'الباقات والأسعار';
$page_description = 'تعرف على باقاتنا وأسعارنا المميزة لخدمات طب الأسنان';


// Fetch menu items
try {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'header' ORDER BY display_order ASC");
    $stmt->execute();
    $header_menu = $stmt->fetchAll();
} catch(PDOException $e) {
    $header_menu = [];
}

try {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_active = 1 AND position = 'footer' ORDER BY display_order ASC");
    $stmt->execute();
    $footer_menu = $stmt->fetchAll();
} catch(PDOException $e) {
    $footer_menu = [];
}
?>
?>
<?php include 'includes/header.php'; ?>

    <style>

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .page-header-section {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.95), rgba(6, 214, 160, 0.95));
            color: white;
            padding: 100px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            opacity: 0.3;
        }

        .page-header-content {
            position: relative;
            z-index: 1;
        }

        .page-header-section h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .page-header-section p {
            font-size: 1.3rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
        }

        .packages-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .section-intro {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 60px;
        }

        .section-intro h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .section-intro p {
            font-size: 1.2rem;
            color: #666;
            line-height: 1.8;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .package-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }

        .package-card.popular {
            border: 3px solid var(--accent-color);
            transform: scale(1.05);
        }

        .package-card.popular::before {
            content: 'الأكثر طلباً';
            position: absolute;
            top: 20px;
            left: -35px;
            background: var(--accent-color);
            color: white;
            padding: 5px 40px;
            transform: rotate(-45deg);
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .package-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }

        .package-name {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .package-price {
            font-size: 3rem;
            font-weight: 900;
            color: var(--accent-color);
            line-height: 1;
        }

        .package-price small {
            font-size: 1rem;
            color: #999;
            font-weight: 400;
        }

        .package-duration {
            color: #666;
            font-size: 0.95rem;
            margin-top: 10px;
        }

        .package-description {
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 25px;
            text-align: center;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .package-features li {
            padding: 12px 0;
            color: #333;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .package-features li i {
            color: var(--accent-color);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .package-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .package-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 180, 216, 0.3);
        }

        .package-card.popular .package-btn {
            background: linear-gradient(135deg, var(--accent-color), #06c494);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .cta-btn {
            background: white;
            color: var(--primary-color);
            padding: 18px 50px;
            font-size: 1.2rem;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .cta-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .page-header-section h1 {
                font-size: 2.2rem;
            }

            .packages-grid {
                grid-template-columns: 1fr;
            }

            .package-card.popular {
                transform: scale(1);
            }
        }
    </style>
    <!-- Include Header -->

    <!-- Page Header -->
    <section class="page-header-section">
        <div class="page-header-content">
            <div class="container">
                <h1><i class="fas fa-box-open"></i> الباقات والأسعار</h1>
                <p>اختر الباقة المناسبة لك من بين مجموعة متنوعة من العروض والخدمات المميزة</p>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <div class="section-intro">
                <h2>اختر الباقة المناسبة لك</h2>
                <p>نقدم لك مجموعة متنوعة من الباقات الطبية المصممة خصيصاً لتلبية احتياجاتك. جميع الباقات تشمل استشارة مجانية وضمان على الخدمات المقدمة.</p>
            </div>

            <?php if (count($packages) > 0): ?>
            <div class="packages-grid">
                <?php foreach ($packages as $package): ?>
                <div class="package-card <?php echo $package['is_popular'] ? 'popular' : ''; ?>">
                    <div class="package-header">
                        <h3 class="package-name"><?php echo htmlspecialchars($package['name']); ?></h3>
                        <div class="package-price">
                            <?php echo number_format($package['price'], 0); ?> <small>جنيه</small>
                        </div>
                        <?php if (!empty($package['duration'])): ?>
                            <div class="package-duration">
                                <i class="fas fa-clock"></i> <?php echo htmlspecialchars($package['duration']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($package['description'])): ?>
                        <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['features'])): ?>
                        <ul class="package-features">
                            <?php
                            $features = explode("\n", $package['features']);
                            foreach ($features as $feature):
                                $feature = trim($feature);
                                if (!empty($feature)):
                            ?>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span><?php echo htmlspecialchars($feature); ?></span>
                                </li>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </ul>
                    <?php endif; ?>

                    <a href="index.php#booking" class="package-btn">
                        <i class="fas fa-calendar-check"></i> احجز الآن
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 20px;">
                    <i class="fas fa-box-open" style="font-size: 5rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: #999; font-size: 1.5rem;">لا توجد باقات متاحة حالياً</h3>
                    <p style="color: #bbb; margin-top: 10px;">نعمل على إضافة باقات جديدة قريباً</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>هل أنت مستعد للبدء؟</h2>
            <p>احجز موعدك الآن واحصل على استشارة مجانية</p>
            <a href="index.php#booking" class="cta-btn">
                <i class="fas fa-calendar-check"></i> احجز موعدك الآن
            </a>
            <div style="margin-top: 30px;">
                <p style="font-size: 1rem; opacity: 0.8;">
                    <i class="fas fa-phone"></i> للاستفسار: <?php echo htmlspecialchars($site_phone); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Include Footer -->

<?php include 'includes/footer.php'; ?>
