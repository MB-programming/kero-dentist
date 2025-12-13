<?php
require_once '../includes/config.php';

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

$site_name = getSetting('site_name', 'Dr. Ahmed Clinic');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المدونة - <?php echo htmlspecialchars($site_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation (same as index.php) -->
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

    <!-- Page Header -->
    <div style="height: 70px;"></div>
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; padding: 60px 20px; text-align: center; margin-top: 70px;">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 15px;">مدونتنا الطبية</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">آخر المقالات والنصائح الطبية</p>
        </div>
    </section>

    <!-- Blog Posts -->
    <section class="blog" style="background: var(--bg-white);">
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
                            <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline" style="border-color: var(--primary-color); color: var(--primary-color);">السابق</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                        <span class="btn btn-primary"><?php echo $i; ?></span>
                        <?php else: ?>
                        <a href="?page=<?php echo $i; ?>" class="btn btn-outline" style="border-color: var(--primary-color); color: var(--primary-color);"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline" style="border-color: var(--primary-color); color: var(--primary-color);">التالي</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-newspaper" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-color); margin-bottom: 10px;">لا توجد مقالات حالياً</h3>
                    <p style="color: var(--text-light);">تابعونا قريباً للحصول على آخر المقالات والنصائح الطبية</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer (same as index.php) -->
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
