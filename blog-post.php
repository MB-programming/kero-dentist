<?php
require_once 'includes/config.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

// Fetch blog post
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.php');
    exit;
}

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');

// Meta tags
$meta_title = !empty($post['meta_title']) ? $post['meta_title'] : $post['title'];
$meta_description = !empty($post['meta_description']) ? $post['meta_description'] : $post['excerpt'];
$meta_keywords = $post['meta_keywords'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($meta_title); ?> - <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php if ($meta_keywords): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <?php endif; ?>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php if ($post['featured_image']): ?>
    <meta property="og:image" content="<?php echo UPLOAD_URL . htmlspecialchars($post['featured_image']); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .post-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .post-content h2 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        .post-content h3 {
            margin-top: 25px;
            margin-bottom: 12px;
            color: var(--text-color);
        }
        .post-content p {
            margin-bottom: 20px;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }
        .post-content ul, .post-content ol {
            margin-bottom: 20px;
            padding-right: 30px;
        }
        .post-content li {
            margin-bottom: 10px;
        }
        .post-content blockquote {
            border-right: 4px solid var(--primary-color);
            padding: 20px;
            margin: 20px 0;
            background: var(--bg-light);
            border-radius: 5px;
        }
    </style>
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
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="index.php#about">عن الدكتور</a></li>
                <li><a href="index.php#services">الخدمات</a></li>
                <li><a href="index.php#packages">الباقات</a></li>
                <li><a href="blog.php">المقالات</a></li>
                <li><a href="index.php#reviews">آراء العملاء</a></li>
                <li><a href="index.php#booking" class="btn-nav">احجز الآن</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <div style="height: 70px;"></div>

    <!-- Blog Post -->
    <article style="padding: 80px 20px; background: var(--bg-white);">
        <div class="container" style="max-width: 800px;">
            <!-- Breadcrumb -->
            <div style="margin-bottom: 30px; color: var(--text-light);">
                <a href="index.php" style="color: var(--text-light); text-decoration: none;">الرئيسية</a>
                <i class="fas fa-chevron-left" style="margin: 0 10px; font-size: 0.8rem;"></i>
                <a href="blog.php" style="color: var(--text-light); text-decoration: none;">المدونة</a>
                <i class="fas fa-chevron-left" style="margin: 0 10px; font-size: 0.8rem;"></i>
                <span><?php echo htmlspecialchars($post['title']); ?></span>
            </div>

            <!-- Post Header -->
            <header style="margin-bottom: 40px;">
                <h1 style="font-size: 2.5rem; color: var(--text-color); margin-bottom: 20px; line-height: 1.3;">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                <div class="blog-meta" style="display: flex; gap: 20px; color: var(--text-light); border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
                    <span><i class="fas fa-calendar"></i> <?php echo formatDate($post['published_at']); ?></span>
                    <?php if ($post['author']): ?>
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if ($post['featured_image']): ?>
            <div style="margin-bottom: 40px;">
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($post['featured_image']); ?>"
                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                     style="width: 100%; border-radius: 15px; box-shadow: var(--shadow-lg);">
            </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="post-content">
                <?php echo $post['content']; ?>
            </div>

            <!-- Share Buttons -->
            <div style="margin-top: 50px; padding: 30px; background: var(--bg-light); border-radius: 10px; text-align: center;">
                <h3 style="margin-bottom: 20px; color: var(--text-color);">شارك المقال</h3>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>"
                       target="_blank"
                       class="btn btn-primary"
                       style="background: #1877f2;">
                        <i class="fab fa-facebook"></i> فيسبوك
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>"
                       target="_blank"
                       class="btn btn-primary"
                       style="background: #1da1f2;">
                        <i class="fab fa-twitter"></i> تويتر
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . SITE_URL . '/blog-post.php?slug=' . $post['slug']); ?>"
                       target="_blank"
                       class="btn btn-primary"
                       style="background: #25d366;">
                        <i class="fab fa-whatsapp"></i> واتساب
                    </a>
                </div>
            </div>

            <!-- Back to Blog -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="blog.php" class="btn btn-outline" style="border-color: var(--primary-color); color: var(--primary-color);">
                    <i class="fas fa-arrow-right"></i> العودة إلى المدونة
                </a>
            </div>
        </div>
    </article>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('doctor_bio', '')); ?></p>
                </div>
                <div class="footer-links">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="index.php#about">عن الدكتور</a></li>
                        <li><a href="index.php#services">الخدمات</a></li>
                        <li><a href="blog.php">المقالات</a></li>
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
