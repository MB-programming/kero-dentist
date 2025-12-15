<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    require_once 'includes/config.php';
    $site_name = getSetting('site_name', 'عيادة الدكتور');
    echo 'آراء العملاء - ' . htmlspecialchars($site_name);
    ?></title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts - Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .reviews-hero {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.1), rgba(6, 214, 160, 0.1));
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .reviews-hero h1 {
            font-size: 3rem;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .reviews-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin: 2rem 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .review-card {
            background: var(--white);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .review-card::before {
            content: '"';
            position: absolute;
            top: 0;
            right: 1rem;
            font-size: 8rem;
            color: rgba(0, 180, 216, 0.05);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .review-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .review-info {
            flex: 1;
        }

        .review-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .review-rating {
            color: #FFB800;
            font-size: 1.1rem;
        }

        .review-date {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .review-text {
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 1.05rem;
            position: relative;
            z-index: 1;
        }

        .review-service {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--gray-200);
        }

        .review-service-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .review-service-name {
            color: var(--primary-color);
            font-weight: 600;
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--gray-300);
            background: var(--white);
            color: var(--text-primary);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
        }

        .write-review-cta {
            text-align: center;
            padding: 4rem 0;
            background: var(--gray-50);
            margin-top: 4rem;
        }

        .write-review-cta h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .reviews-hero h1 {
                font-size: 2rem;
            }

            .reviews-stats {
                gap: 2rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .reviews-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
    // Fetch all approved reviews
    $stmt = $conn->prepare("SELECT r.*, p.name as package_name
                            FROM reviews r
                            LEFT JOIN packages p ON r.package_id = p.id
                            WHERE r.is_approved = 1 AND r.is_displayed = 1
                            ORDER BY r.created_at DESC");
    $stmt->execute();
    $reviews = $stmt->fetchAll();

    // Calculate stats
    $total_reviews = count($reviews);
    $total_rating = 0;
    $rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
        $rating_counts[$review['rating']]++;
    }

    $avg_rating = $total_reviews > 0 ? round($total_rating / $total_reviews, 1) : 0;
    ?>

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-contact">
                        <div class="header-contact-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars(getSetting('site_phone', '+20 123 456 7890')); ?></span>
                        </div>
                        <div class="header-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars(getSetting('site_email', 'info@clinic.com')); ?></span>
                        </div>
                    </div>
                    <div class="header-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
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
                        <li><a href="doctor.php" class="nav-link">عن الدكتور</a></li>
                        <li><a href="reviews.php" class="nav-link active">آراء العملاء</a></li>
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

    <!-- Reviews Hero -->
    <section class="reviews-hero">
        <div class="container">
            <h1>آراء عملائنا</h1>
            <p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 700px; margin: 1rem auto;">
                نفخر بثقة عملائنا ونسعد بمشاركة تجاربهم معنا
            </p>

            <div class="reviews-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_reviews; ?>+</span>
                    <span class="stat-label">تقييم</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $avg_rating; ?></span>
                    <span class="stat-label">متوسط التقييم</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $rating_counts[5]; ?></span>
                    <span class="stat-label">5 نجوم</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">جميع التقييمات</button>
                <button class="filter-btn" data-filter="5">5 نجوم</button>
                <button class="filter-btn" data-filter="4">4 نجوم</button>
                <button class="filter-btn" data-filter="3">3 نجوم</button>
            </div>

            <!-- Reviews Grid -->
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                <div class="review-card" data-rating="<?php echo $review['rating']; ?>">
                    <div class="review-header">
                        <div class="review-avatar">
                            <?php echo mb_substr($review['client_name'], 0, 1); ?>
                        </div>
                        <div class="review-info">
                            <div class="review-name"><?php echo htmlspecialchars($review['client_name']); ?></div>
                            <div class="review-rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <?php if ($i < $review['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="review-date">
                                <?php echo formatDate($review['created_at'], 'd F Y'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="review-text">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </div>

                    <?php if ($review['package_name']): ?>
                    <div class="review-service">
                        <div class="review-service-label">الخدمة:</div>
                        <div class="review-service-name"><?php echo htmlspecialchars($review['package_name']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($reviews) === 0): ?>
            <div class="text-center" style="padding: 4rem 0;">
                <i class="fas fa-comments" style="font-size: 5rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--text-secondary);">لا توجد تقييمات بعد</h3>
                <p style="color: var(--text-muted);">كن أول من يشارك تجربته معنا</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Write Review CTA -->
    <section class="write-review-cta">
        <div class="container">
            <h2>هل لديك تجربة معنا؟</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">
                نسعد بمشاركة رأيك وتقييمك لخدماتنا
            </p>
            <a href="index.php#booking" class="btn btn-primary" style="font-size: 1.1rem;">
                <i class="fas fa-star"></i>
                احجز موعد
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><?php echo htmlspecialchars($site_name); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('doctor_bio', 'عيادة أسنان متخصصة')); ?></p>
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
                            <?php echo htmlspecialchars(getSetting('site_phone', '+20 123 456 7890')); ?>
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars(getSetting('site_email', 'info@clinic.com')); ?>
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars(getSetting('site_address', 'Cairo, Egypt')); ?>
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
    <script>
        // Filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const reviewCards = document.querySelectorAll('.review-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;

                reviewCards.forEach(card => {
                    if (filter === 'all' || card.dataset.rating === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
