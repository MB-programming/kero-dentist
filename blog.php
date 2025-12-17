<?php
require_once 'includes/config.php';

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Get total count
$stmt = $conn->prepare("SELECT COUNT(*) FROM blog_posts WHERE is_published = 1");
$stmt->execute();
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Fetch blog posts
$stmt = $conn->prepare("
    SELECT * FROM blog_posts
    WHERE is_published = 1
    ORDER BY published_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$per_page, $offset]);
$blog_posts = $stmt->fetchAll();

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
$doctor_bio = getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان');
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
    <title>المدونة - <?php echo htmlspecialchars($site_name); ?></title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/modern-frontend.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .blog-hero {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.95), rgba(6, 214, 160, 0.95));
            color: white;
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .blog-hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .blog-hero p {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 32px;
            margin-top: 50px;
        }

        .blog-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.15);
        }

        .blog-image {
            width: 100%;
            height: 240px;
            overflow: hidden;
            position: relative;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .blog-card:hover .blog-image img {
            transform: scale(1.1);
        }

        .blog-content {
            padding: 28px;
        }

        .blog-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .blog-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .blog-meta i {
            color: var(--primary);
        }

        .blog-card h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .blog-card p {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .read-more:hover {
            gap: 12px;
            color: var(--primary-dark);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 60px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            min-width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .pagination a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .pagination span {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        @media (max-width: 768px) {
            .blog-hero h1 {
                font-size: 2.5rem;
            }

            .blog-grid {
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
                        <li><a href="doctor.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                        <li><a href="doctor.php#services"><i class="fas fa-tooth"></i> الخدمات</a></li>
                        <li><a href="doctor.php#packages"><i class="fas fa-box"></i> الباقات</a></li>
                        <li><a href="blog.php" class="active"><i class="fas fa-blog"></i> المدونة</a></li>
                        <li><a href="doctor.php#booking" class="modern-cta-btn"><i class="fas fa-calendar-check"></i> احجز الآن</a></li>
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

    <!-- Blog Hero -->
    <section class="blog-hero">
        <div class="container">
            <h1>مدونتنا الطبية</h1>
            <p>آخر المقالات والنصائح الطبية</p>
        </div>
    </section>

    <!-- Blog Posts -->
    <section class="modern-section" style="background: var(--bg-light);">
        <div class="container">
            <?php if (count($blog_posts) > 0): ?>
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
                            <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">
                                اقرأ المزيد <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">السابق</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                        <span><?php echo $i; ?></span>
                        <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">التالي</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <p>لا توجد مقالات حالياً</p>
                </div>
            <?php endif; ?>
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
                            <li><a href="doctor.php">الرئيسية</a></li>
                            <li><a href="doctor.php#services">الخدمات</a></li>
                            <li><a href="doctor.php#packages">الباقات</a></li>
                            <li><a href="blog.php">المدونة</a></li>
                            <li><a href="doctor.php#booking">احجز الآن</a></li>
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
                    <a href="doctor.php#booking" class="modern-btn modern-btn-primary" style="margin-top: 20px;">
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

    <script>
    // Mobile menu toggle
    document.getElementById('modernHamburger').addEventListener('click', function() {
        document.getElementById('modernNavMenu').classList.toggle('active');
    });
    </script>
</body>
</html>
