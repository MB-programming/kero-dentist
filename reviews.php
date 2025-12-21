<?php
require_once 'includes/config.php';

// Fetch all approved reviews
$stmt = $conn->prepare("SELECT * FROM reviews WHERE is_approved = 1 AND is_displayed = 1 ORDER BY created_at DESC");
$stmt->execute();
$all_reviews = $stmt->fetchAll();

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
$page_title = 'آراء العملاء';
$page_description = 'اقرأ آراء وتقييمات عملائنا السعداء عن خدماتنا في طب الأسنان';

// Calculate stats
$total_reviews = count($all_reviews);
$average_rating = 0;
if ($total_reviews > 0) {
    $total_rating = 0;
    foreach ($all_reviews as $review) {
        $total_rating += $review['rating'];
    }
    $average_rating = round($total_rating / $total_reviews, 1);
}

// Count by rating
$rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
foreach ($all_reviews as $review) {
    $rating_counts[$review['rating']]++;
}
?>
<?php include 'includes/header.php'; ?>

    <style>
        .reviews-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 140px 0 80px;
            position: relative;
            overflow: hidden;
            color: white;
            text-align: center;
        }

        .reviews-hero::before {
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

        .reviews-hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            position: relative;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .reviews-hero p {
            font-size: 1.3rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }

        .reviews-stats {
            background: white;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin: -80px auto 60px;
            max-width: 900px;
            position: relative;
            z-index: 10;
        }

        .reviews-stats-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .average-rating {
            font-size: 4rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .average-rating-stars {
            font-size: 2rem;
            color: #f59e0b;
            margin-bottom: 10px;
        }

        .total-reviews-text {
            font-size: 1.2rem;
            color: #64748b;
            font-weight: 600;
        }

        .rating-breakdown {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }

        .rating-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .rating-row-label {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
            font-weight: 600;
            color: #2d3748;
        }

        .rating-bar-container {
            flex: 1;
            height: 12px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .rating-bar {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .rating-row-count {
            min-width: 50px;
            text-align: left;
            color: #64748b;
            font-weight: 600;
        }

        .reviews-section {
            padding: 60px 0;
        }

        .all-reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .reviews-hero h1 {
                font-size: 2.5rem;
            }

            .reviews-stats {
                margin: -50px 20px 40px;
                padding: 35px 25px;
            }

            .average-rating {
                font-size: 3rem;
            }

            .all-reviews-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="reviews-hero">
        <div class="container">
            <h1>آراء عملائنا السعداء</h1>
            <p>نفخر بثقة عملائنا ورضاهم عن خدماتنا. اقرأ تجاربهم الحقيقية معنا</p>
        </div>
    </section>

    <!-- Statistics Section -->
    <div class="container">
        <div class="reviews-stats">
            <div class="reviews-stats-header">
                <div class="average-rating"><?php echo $average_rating; ?></div>
                <div class="average-rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= floor($average_rating)): ?>
                            <i class="fas fa-star"></i>
                        <?php elseif ($i <= ceil($average_rating) && $average_rating - floor($average_rating) >= 0.5): ?>
                            <i class="fas fa-star-half-alt"></i>
                        <?php else: ?>
                            <i class="far fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="total-reviews-text">بناءً على <?php echo $total_reviews; ?> تقييم</div>
            </div>

            <?php if ($total_reviews > 0): ?>
            <div class="rating-breakdown">
                <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                <div class="rating-row">
                    <div class="rating-row-label">
                        <span><?php echo $rating; ?> نجوم</span>
                        <i class="fas fa-star" style="color: #f59e0b; font-size: 0.9rem;"></i>
                    </div>
                    <div class="rating-bar-container">
                        <div class="rating-bar" style="width: <?php echo $total_reviews > 0 ? ($rating_counts[$rating] / $total_reviews * 100) : 0; ?>%;"></div>
                    </div>
                    <div class="rating-row-count"><?php echo $rating_counts[$rating]; ?></div>
                </div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- All Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <?php if (count($all_reviews) > 0): ?>
            <div class="all-reviews-grid">
                <?php foreach ($all_reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="review-info">
                            <h4 class="review-name"><?php echo htmlspecialchars($review['client_name']); ?></h4>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $review['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                    <div class="review-footer">
                        <i class="fas fa-check-circle"></i>
                        <span>عميل معتمد</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 80px 20px;">
                <i class="fas fa-comments" style="font-size: 5rem; color: #cbd5e0; margin-bottom: 25px;"></i>
                <h3 style="font-size: 1.8rem; color: #4a5568; margin-bottom: 15px;">لا توجد تقييمات بعد</h3>
                <p style="font-size: 1.1rem; color: #64748b;">كن أول من يشارك تجربته معنا</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="modern-section" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; text-align: center; padding: 80px 0;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 25px; color: white;">هل جربت خدماتنا؟</h2>
            <p style="font-size: 1.3rem; margin-bottom: 40px; opacity: 0.95;">شاركنا رأيك وساعد الآخرين في اتخاذ قرارهم</p>
            <a href="index.php#booking" class="btn" style="background: white; color: var(--primary); padding: 18px 50px; font-size: 1.2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-calendar-check"></i> احجز موعد الآن
            </a>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
