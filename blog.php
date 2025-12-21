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

// Page title for header
$page_title = 'المدونة';
$page_description = 'مقالات ونصائح طبية عن صحة الفم والأسنان';

?>
<?php include 'includes/header.php'; ?>

    <style>

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
<?php include 'includes/footer.php'; ?>