-- =============================================
-- Database Schema for Doctor Booking Website
-- استورد هذا الملف مباشرة في قاعدة البيانات الخاصة بك
-- Import this file directly into your existing database
-- =============================================

-- Admin Users Table
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services Table
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(100),
    `display_order` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Packages Table
CREATE TABLE IF NOT EXISTS `packages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `duration` VARCHAR(100),
    `features` TEXT,
    `is_popular` BOOLEAN DEFAULT FALSE,
    `is_active` BOOLEAN DEFAULT TRUE,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `client_name` VARCHAR(200) NOT NULL,
    `client_email` VARCHAR(150),
    `client_address` TEXT,
    `client_phone` VARCHAR(20) NOT NULL,
    `client_whatsapp` VARCHAR(20) NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_day` VARCHAR(20) NOT NULL,
    `package_id` INT,
    `status` ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    `whatsapp_sent` BOOLEAN DEFAULT FALSE,
    `email_sent` BOOLEAN DEFAULT FALSE,
    `admin_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_booking_date` (`booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `excerpt` TEXT,
    `featured_image` VARCHAR(255),
    `author` VARCHAR(100),
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `meta_keywords` TEXT,
    `is_published` BOOLEAN DEFAULT FALSE,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_published` (`is_published`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `client_name` VARCHAR(200) NOT NULL,
    `client_email` VARCHAR(150),
    `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `review_text` TEXT NOT NULL,
    `is_approved` BOOLEAN DEFAULT FALSE,
    `is_displayed` BOOLEAN DEFAULT FALSE,
    `source` ENUM('email', 'manual') DEFAULT 'manual',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_approved` (`is_approved`, `is_displayed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` VARCHAR(50) DEFAULT 'text',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sliders/Banners Table
CREATE TABLE IF NOT EXISTS `sliders` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` TEXT,
    `image` VARCHAR(255) NOT NULL,
    `button_text` VARCHAR(100),
    `button_link` VARCHAR(255),
    `display_order` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email Templates Table
CREATE TABLE IF NOT EXISTS `email_templates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `template_key` VARCHAR(100) NOT NULL UNIQUE,
    `template_name` VARCHAR(200) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` LONGTEXT NOT NULL,
    `available_variables` TEXT,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menu Items Table
CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `position` ENUM('header', 'footer') DEFAULT 'header',
    `display_order` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `target` ENUM('_self', '_blank') DEFAULT '_self',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_position_order` (`position`, `display_order`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Insert Default Data
-- =============================================

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO `admin_users` (`username`, `password`, `email`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('site_name', 'Dr. Ahmed Clinic', 'text'),
('site_logo', '', 'text'),
('site_email', 'info@drahmed.com', 'text'),
('site_phone', '+20 123 456 7890', 'text'),
('site_address', 'Cairo, Egypt', 'text'),
('whatsapp_api_token', '', 'text'),
('whatsapp_api_url', '', 'text'),
('gmail_api_client_id', '', 'text'),
('gmail_api_client_secret', '', 'text'),
('reviews_email', 'reviews@drahmed.com', 'text'),
('doctor_name', 'Dr. Ahmed Mohamed', 'text'),
('doctor_title', 'Dental Specialist', 'text'),
('doctor_bio', 'Experienced dental specialist with over 10 years of practice.', 'textarea'),
('hero_title', 'Your Smile, Our Priority', 'text'),
('hero_subtitle', 'Professional dental care for you and your family', 'text'),
('smtp_host', '', 'text'),
('smtp_port', '587', 'text'),
('smtp_username', '', 'text'),
('smtp_password', '', 'password'),
('smtp_from_email', '', 'text'),
('smtp_from_name', '', 'text'),
('smtp_encryption', 'tls', 'text'),
('enable_email_notifications', '0', 'boolean'),
('doctor_image', '', 'text'),
('tinymce_api_key', '', 'text'),
('google_maps_api_key', '', 'text'),
('facebook_pixel_id', '', 'text'),
('google_analytics_id', '', 'text'),
('about_title', 'من نحن', 'text'),
('about_content', '', 'textarea'),
('about_image', '', 'text'),
('contact_title', 'تواصل معنا', 'text'),
('contact_description', 'نسعد بتواصلكم معنا', 'textarea'),
('contact_map_embed', '', 'textarea')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- Insert sample services
INSERT INTO `services` (`title`, `description`, `icon`, `display_order`) VALUES
('General Dentistry', 'Comprehensive dental care including checkups, cleanings, and preventive treatments.', 'fa-tooth', 1),
('Cosmetic Dentistry', 'Teeth whitening, veneers, and smile makeovers.', 'fa-smile', 2),
('Orthodontics', 'Braces and aligners for a perfect smile.', 'fa-teeth', 3),
('Dental Implants', 'Permanent tooth replacement solutions.', 'fa-x-ray', 4)
ON DUPLICATE KEY UPDATE `title` = `title`;

-- Insert sample packages
INSERT INTO `packages` (`name`, `description`, `price`, `duration`, `features`, `is_popular`) VALUES
('Basic Checkup', 'Complete dental examination and cleaning', 500.00, '1 Hour', 'Dental Examination\nProfessional Cleaning\nX-Ray (if needed)\nConsultation', FALSE),
('Teeth Whitening', 'Professional teeth whitening treatment', 1500.00, '1-2 Hours', 'Professional Whitening\nBefore/After Photos\nMaintenance Kit\nFollow-up Consultation', TRUE),
('Complete Care', 'Comprehensive dental care package', 3000.00, '3 Months', 'Full Examination\nCleaning Sessions\nX-Rays\nMinor Treatments\nEmergency Support', FALSE)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Insert default email templates
INSERT INTO `email_templates` (`template_key`, `template_name`, `subject`, `body`, `available_variables`, `description`) VALUES
('booking_pending', 'Booking Pending - Client', 'تأكيد استلام طلب الحجز - {{SITE_NAME}}',
'<div style="font-family: Arial, sans-serif; direction: rtl; text-align: right; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #2563eb, #1e40af); padding: 30px; text-align: center; color: white;">
        <h1>{{SITE_NAME}}</h1>
    </div>
    <div style="padding: 30px; background: #f9fafb;">
        <h2 style="color: #1f2937;">مرحباً {{CLIENT_NAME}}،</h2>
        <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
            شكراً لك على حجزك معنا! تم استلام طلبك بنجاح وهو الآن قيد المراجعة.
        </p>
        <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h3 style="color: #2563eb; margin-bottom: 15px;">تفاصيل الحجز:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>التاريخ:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{BOOKING_DATE}}</td></tr>
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>اليوم:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{BOOKING_DAY}}</td></tr>
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>الباقة:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{PACKAGE_NAME}}</td></tr>
                <tr><td style="padding: 10px;"><strong>رقم الحجز:</strong></td><td style="padding: 10px;">{{BOOKING_ID}}</td></tr>
            </table>
        </div>
        <p style="color: #6b7280; font-size: 14px;">سنقوم بالتواصل معك قريباً لتأكيد الموعد.</p>
    </div>
    <div style="background: #1f2937; padding: 20px; text-align: center; color: #9ca3af; font-size: 14px;">
        <p>{{SITE_NAME}} - {{SITE_ADDRESS}}</p>
        <p>{{SITE_PHONE}} | {{SITE_EMAIL}}</p>
    </div>
</div>',
'{{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{CLIENT_PHONE}}, {{CLIENT_WHATSAPP}}, {{BOOKING_DATE}}, {{BOOKING_DAY}}, {{PACKAGE_NAME}}, {{BOOKING_ID}}, {{SITE_NAME}}, {{SITE_EMAIL}}, {{SITE_PHONE}}, {{SITE_ADDRESS}}',
'Email sent to client when booking is first received'),

('booking_approved', 'Booking Approved - Client', 'تم تأكيد حجزك - {{SITE_NAME}}',
'<div style="font-family: Arial, sans-serif; direction: rtl; text-align: right; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 30px; text-align: center; color: white;">
        <h1>{{SITE_NAME}}</h1>
        <h2 style="margin-top: 15px;">✅ تم تأكيد حجزك!</h2>
    </div>
    <div style="padding: 30px; background: #f9fafb;">
        <h2 style="color: #1f2937;">عزيزي {{CLIENT_NAME}}،</h2>
        <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
            يسعدنا إبلاغك بأن حجزك قد تم تأكيده بنجاح! نحن في انتظارك.
        </p>
        <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0; border-right: 4px solid #10b981;">
            <h3 style="color: #10b981; margin-bottom: 15px;">📋 تفاصيل موعدك:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>التاريخ:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{BOOKING_DATE}}</td></tr>
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>اليوم:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{BOOKING_DAY}}</td></tr>
                <tr><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;"><strong>الباقة:</strong></td><td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{PACKAGE_NAME}}</td></tr>
                <tr><td style="padding: 10px;"><strong>رقم الحجز:</strong></td><td style="padding: 10px;">{{BOOKING_ID}}</td></tr>
            </table>
        </div>
        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0; color: #92400e;"><strong>⚠️ ملحوظة مهمة:</strong> يرجى الحضور قبل الموعد بـ 15 دقيقة.</p>
        </div>
    </div>
    <div style="background: #1f2937; padding: 20px; text-align: center; color: #9ca3af; font-size: 14px;">
        <p>{{SITE_NAME}} - {{SITE_ADDRESS}}</p>
        <p>{{SITE_PHONE}} | {{SITE_EMAIL}}</p>
    </div>
</div>',
'{{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{CLIENT_PHONE}}, {{CLIENT_WHATSAPP}}, {{BOOKING_DATE}}, {{BOOKING_DAY}}, {{PACKAGE_NAME}}, {{BOOKING_ID}}, {{SITE_NAME}}, {{SITE_EMAIL}}, {{SITE_PHONE}}, {{SITE_ADDRESS}}',
'Email sent to client when booking is approved'),

('booking_rejected', 'Booking Rejected - Client', 'تحديث بخصوص حجزك - {{SITE_NAME}}',
'<div style="font-family: Arial, sans-serif; direction: rtl; text-align: right; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #ef4444, #dc2626); padding: 30px; text-align: center; color: white;">
        <h1>{{SITE_NAME}}</h1>
    </div>
    <div style="padding: 30px; background: #f9fafb;">
        <h2 style="color: #1f2937;">عزيزي {{CLIENT_NAME}}،</h2>
        <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
            نأسف لإبلاغك بأننا غير قادرين على تأكيد حجزك في الوقت المحدد.
        </p>
        <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <p style="color: #6b7280;">يمكنك التواصل معنا لاختيار موعد بديل يناسبك.</p>
        </div>
        <div style="text-align: center; margin: 25px 0;">
            <a href="tel:{{SITE_PHONE}}" style="background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">اتصل بنا الآن</a>
        </div>
    </div>
    <div style="background: #1f2937; padding: 20px; text-align: center; color: #9ca3af; font-size: 14px;">
        <p>{{SITE_NAME}} - {{SITE_ADDRESS}}</p>
        <p>{{SITE_PHONE}} | {{SITE_EMAIL}}</p>
    </div>
</div>',
'{{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{CLIENT_PHONE}}, {{CLIENT_WHATSAPP}}, {{BOOKING_DATE}}, {{BOOKING_DAY}}, {{PACKAGE_NAME}}, {{BOOKING_ID}}, {{SITE_NAME}}, {{SITE_EMAIL}}, {{SITE_PHONE}}, {{SITE_ADDRESS}}',
'Email sent to client when booking is rejected'),

('booking_admin_notification', 'New Booking - Admin', 'حجز جديد - {{BOOKING_ID}}',
'<div style="font-family: Arial, sans-serif; direction: rtl; text-align: right; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #6366f1, #4f46e5); padding: 30px; text-align: center; color: white;">
        <h1>🔔 حجز جديد!</h1>
    </div>
    <div style="padding: 30px; background: #f9fafb;">
        <h3 style="color: #1f2937;">تفاصيل العميل:</h3>
        <table style="width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden;">
            <tr style="background: #f3f4f6;"><td style="padding: 12px; font-weight: bold;">الاسم:</td><td style="padding: 12px;">{{CLIENT_NAME}}</td></tr>
            <tr><td style="padding: 12px; font-weight: bold;">البريد الإلكتروني:</td><td style="padding: 12px;">{{CLIENT_EMAIL}}</td></tr>
            <tr style="background: #f3f4f6;"><td style="padding: 12px; font-weight: bold;">الهاتف:</td><td style="padding: 12px;">{{CLIENT_PHONE}}</td></tr>
            <tr><td style="padding: 12px; font-weight: bold;">واتساب:</td><td style="padding: 12px;">{{CLIENT_WHATSAPP}}</td></tr>
            <tr style="background: #f3f4f6;"><td style="padding: 12px; font-weight: bold;">العنوان:</td><td style="padding: 12px;">{{CLIENT_ADDRESS}}</td></tr>
        </table>

        <h3 style="color: #1f2937; margin-top: 25px;">تفاصيل الحجز:</h3>
        <table style="width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden;">
            <tr style="background: #f3f4f6;"><td style="padding: 12px; font-weight: bold;">رقم الحجز:</td><td style="padding: 12px;">{{BOOKING_ID}}</td></tr>
            <tr><td style="padding: 12px; font-weight: bold;">التاريخ:</td><td style="padding: 12px;">{{BOOKING_DATE}}</td></tr>
            <tr style="background: #f3f4f6;"><td style="padding: 12px; font-weight: bold;">اليوم:</td><td style="padding: 12px;">{{BOOKING_DAY}}</td></tr>
            <tr><td style="padding: 12px; font-weight: bold;">الباقة:</td><td style="padding: 12px;">{{PACKAGE_NAME}}</td></tr>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ADMIN_URL}}" style="background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block;">عرض في لوحة التحكم</a>
        </div>
    </div>
</div>',
'{{CLIENT_NAME}}, {{CLIENT_EMAIL}}, {{CLIENT_PHONE}}, {{CLIENT_WHATSAPP}}, {{CLIENT_ADDRESS}}, {{BOOKING_DATE}}, {{BOOKING_DAY}}, {{PACKAGE_NAME}}, {{BOOKING_ID}}, {{ADMIN_URL}}, {{SITE_NAME}}',
'Email sent to admin when a new booking is received')
ON DUPLICATE KEY UPDATE `template_key` = `template_key`;

-- Insert default menu items
INSERT INTO `menu_items` (`title`, `url`, `position`, `display_order`, `is_active`) VALUES
('الرئيسية', 'index.php', 'header', 1, 1),
('عن الدكتور', 'index.php#about', 'header', 2, 1),
('الخدمات', 'services.php', 'header', 3, 1),
('الباقات', 'packages.php', 'header', 4, 1),
('المقالات', 'blog.php', 'header', 5, 1),
('آراء العملاء', 'index.php#reviews', 'header', 6, 1),
('الرئيسية', 'index.php', 'footer', 1, 1),
('الخدمات', 'services.php', 'footer', 2, 1),
('الباقات', 'packages.php', 'footer', 3, 1),
('المقالات', 'blog.php', 'footer', 4, 1)
ON DUPLICATE KEY UPDATE `title` = `title`;

-- =============================================
-- End of SQL File
-- تم بنجاح! يمكنك الآن استخدام الموقع
-- =============================================
