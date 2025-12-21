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
                    <p class="footer-text"><?php echo htmlspecialchars($doctor_bio ?? getSetting('doctor_bio', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان')); ?></p>
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
                            <!-- Default footer menu -->
                            <li><a href="index.php">الرئيسية</a></li>
                            <li><a href="about.php">من نحن</a></li>
                            <li><a href="packages.php">الباقات</a></li>
                            <li><a href="blog.php">المدونة</a></li>
                            <li><a href="reviews.php">آراء العملاء</a></li>
                            <li><a href="index.php#booking">احجز الآن</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">معلومات الاتصال</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($site_address ?? getSetting('site_address', 'القاهرة، مصر')); ?>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo htmlspecialchars($site_phone ?? getSetting('site_phone', '+20 123 456 7890')); ?>"><?php echo htmlspecialchars($site_phone ?? getSetting('site_phone', '+20 123 456 7890')); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($site_email ?? getSetting('site_email', 'info@example.com')); ?>"><?php echo htmlspecialchars($site_email ?? getSetting('site_email', 'info@example.com')); ?></a>
                        </li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">ساعات العمل</h3>
                    <ul class="footer-hours">
                        <li><strong>السبت - الخميس:</strong> 9:00 ص - 9:00 م</li>
                        <li><strong>الجمعة:</strong> مغلق</li>
                    </ul>
                    <a href="index.php#booking" class="modern-btn modern-btn-primary" style="margin-top: 20px;">
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
    const hamburger = document.getElementById('modernHamburger');
    const navMenu = document.getElementById('modernNavMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            // If we're on a different page and the link is to index.php#something, allow normal navigation
            if (href.includes('#') && !window.location.pathname.includes('index.php') && this.getAttribute('href').includes('index.php')) {
                return;
            }

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
                if (navMenu) navMenu.classList.remove('active');
            }
        });
    });
    </script>
</body>
</html>
