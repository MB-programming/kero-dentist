<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?><?php echo htmlspecialchars($site_name); ?></title>

    <?php if (isset($page_description)): ?>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <?php
    $site_favicon = getSetting('site_favicon');
    if ($site_favicon && file_exists(UPLOAD_PATH . $site_favicon)):
    ?>
    <link rel="icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo UPLOAD_URL . htmlspecialchars($site_favicon); ?>">
    <?php endif; ?>

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
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <div class="modern-nav-content">
                <a href="index.php" class="modern-logo">
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
                        <!-- Default menu if no items in database -->
                        <li><a href="index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                        <li><a href="about.php"><i class="fas fa-info-circle"></i> من نحن</a></li>
                        <li><a href="packages.php"><i class="fas fa-box"></i> الباقات</a></li>
                        <li><a href="blog.php"><i class="fas fa-blog"></i> المدونة</a></li>
                        <li><a href="reviews.php"><i class="fas fa-star"></i> آراء العملاء</a></li>
                        <li><a href="index.php#booking" class="modern-cta-btn"><i class="fas fa-calendar-check"></i> احجز الآن</a></li>
                    <?php endif; ?>
                </ul>

                <div class="modern-nav-actions">
                    <a href="index.php#booking" class="modern-booking-btn">
                        <i class="fas fa-calendar-check"></i>
                        <span>احجز الآن</span>
                    </a>

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
