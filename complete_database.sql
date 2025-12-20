-- =============================================
-- Kero Dentist - Complete Database Schema
-- قاعدة البيانات الكاملة للمشروع
-- Database: u186120816_kero_dentist
-- Created: December 2025
-- =============================================

-- استخدام قاعدة البيانات
USE `u186120816_kero_dentist`;

-- =============================================
-- Core Website Tables
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
    `image` VARCHAR(255),
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

-- =============================================
-- Bookings System Tables (Enhanced)
-- =============================================

-- Bookings Table (Enhanced with Carrier Detection)
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `client_name` VARCHAR(255) NOT NULL COMMENT 'اسم العميل',
    `client_email` VARCHAR(255) DEFAULT NULL COMMENT 'البريد الإلكتروني',
    `client_address` TEXT DEFAULT NULL COMMENT 'العنوان',
    `client_phone` VARCHAR(50) NOT NULL COMMENT 'رقم الهاتف',
    `client_phone_carrier` VARCHAR(50) DEFAULT NULL COMMENT 'شركة الهاتف (Vodafone, Orange, Etisalat, WE)',
    `client_whatsapp` VARCHAR(50) NOT NULL COMMENT 'رقم الواتساب',
    `client_whatsapp_carrier` VARCHAR(50) DEFAULT NULL COMMENT 'شركة الواتساب',
    `booking_date` DATE NOT NULL COMMENT 'تاريخ الحجز',
    `booking_time` VARCHAR(20) NOT NULL COMMENT 'وقت الحجز',
    `booking_day` VARCHAR(50) DEFAULT NULL COMMENT 'اليوم بالعربي',
    `package_id` INT(11) DEFAULT NULL COMMENT 'رقم الباقة',
    `package_name` VARCHAR(255) DEFAULT NULL COMMENT 'اسم الباقة',
    `package_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'سعر الباقة',
    `status` VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'حالة الحجز: pending, approved, completed, cancelled',
    `whatsapp_sent` BOOLEAN DEFAULT FALSE,
    `email_sent` BOOLEAN DEFAULT FALSE,
    `notes` TEXT DEFAULT NULL COMMENT 'ملاحظات إضافية',
    `admin_notes` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ إنشاء الحجز',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاريخ آخر تحديث',
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_date` (`booking_date`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_phone` (`client_phone`),
    INDEX `idx_whatsapp` (`client_whatsapp`),
    INDEX `idx_package` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول حجوزات العملاء';

-- Booking History Log
CREATE TABLE IF NOT EXISTS `booking_history` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `booking_id` INT(11) NOT NULL COMMENT 'رقم الحجز',
    `old_status` VARCHAR(50) DEFAULT NULL COMMENT 'الحالة القديمة',
    `new_status` VARCHAR(50) NOT NULL COMMENT 'الحالة الجديدة',
    `changed_by` VARCHAR(100) DEFAULT NULL COMMENT 'تم التغيير بواسطة',
    `notes` TEXT DEFAULT NULL COMMENT 'ملاحظات التغيير',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_booking` (`booking_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='سجل تغييرات الحجوزات';

-- Booking Statistics
CREATE TABLE IF NOT EXISTS `booking_stats` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `stat_date` DATE NOT NULL COMMENT 'التاريخ',
    `total_bookings` INT(11) NOT NULL DEFAULT 0 COMMENT 'إجمالي الحجوزات',
    `pending_bookings` INT(11) NOT NULL DEFAULT 0 COMMENT 'الحجوزات المعلقة',
    `approved_bookings` INT(11) NOT NULL DEFAULT 0 COMMENT 'الحجوزات المؤكدة',
    `completed_bookings` INT(11) NOT NULL DEFAULT 0 COMMENT 'الحجوزات المكتملة',
    `cancelled_bookings` INT(11) NOT NULL DEFAULT 0 COMMENT 'الحجوزات الملغاة',
    `total_revenue` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي الإيرادات',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='إحصائيات الحجوزات اليومية';

-- =============================================
-- Content Management Tables
-- =============================================

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

-- Doctors Table
CREATE TABLE IF NOT EXISTS `doctors` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `bio` TEXT NOT NULL,
    `image` VARCHAR(255),
    `specialization` VARCHAR(200),
    `years_experience` INT DEFAULT 0,
    `phone` VARCHAR(20),
    `email` VARCHAR(150),
    `facebook` VARCHAR(255),
    `instagram` VARCHAR(255),
    `twitter` VARCHAR(255),
    `display_order` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_active` (`is_active`),
    INDEX `idx_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Database Views
-- =============================================

-- Active Bookings View
DROP VIEW IF EXISTS `active_bookings`;
CREATE VIEW `active_bookings` AS
SELECT
    b.*,
    DATEDIFF(b.booking_date, CURDATE()) AS days_until_appointment
FROM
    bookings b
WHERE
    b.status IN ('pending', 'approved')
    AND b.booking_date >= CURDATE()
ORDER BY
    b.booking_date ASC,
    b.booking_time ASC;

-- Today's Bookings View
DROP VIEW IF EXISTS `today_bookings`;
CREATE VIEW `today_bookings` AS
SELECT
    b.*
FROM
    bookings b
WHERE
    DATE(b.booking_date) = CURDATE()
ORDER BY
    b.booking_time ASC;

-- Booking Summary View
DROP VIEW IF EXISTS `booking_summary`;
CREATE VIEW `booking_summary` AS
SELECT
    COUNT(*) AS total_bookings,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
    SUM(CASE WHEN status = 'completed' THEN package_price ELSE 0 END) AS total_revenue,
    AVG(CASE WHEN status = 'completed' THEN package_price ELSE NULL END) AS avg_booking_value
FROM
    bookings;

-- Popular Carriers View
DROP VIEW IF EXISTS `popular_carriers`;
CREATE VIEW `popular_carriers` AS
SELECT
    client_phone_carrier AS carrier,
    COUNT(*) AS usage_count
FROM
    bookings
WHERE
    client_phone_carrier IS NOT NULL
GROUP BY
    client_phone_carrier
ORDER BY
    usage_count DESC;

-- =============================================
-- Triggers
-- =============================================

DELIMITER $$

-- Trigger for Booking Status Updates
DROP TRIGGER IF EXISTS `after_booking_status_update`$$
CREATE TRIGGER `after_booking_status_update`
AFTER UPDATE ON `bookings`
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO `booking_history` (
            `booking_id`,
            `old_status`,
            `new_status`,
            `changed_by`,
            `notes`
        ) VALUES (
            NEW.id,
            OLD.status,
            NEW.status,
            'system',
            CONCAT('Status changed from ', OLD.status, ' to ', NEW.status)
        );
    END IF;
END$$

DELIMITER ;

-- =============================================
-- Stored Procedures
-- =============================================

DELIMITER $$

-- Update Booking Status Procedure
DROP PROCEDURE IF EXISTS `update_booking_status`$$
CREATE PROCEDURE `update_booking_status`(
    IN p_booking_id INT,
    IN p_new_status VARCHAR(50),
    IN p_changed_by VARCHAR(100),
    IN p_notes TEXT
)
BEGIN
    DECLARE v_old_status VARCHAR(50);

    SELECT status INTO v_old_status
    FROM bookings
    WHERE id = p_booking_id;

    UPDATE bookings
    SET status = p_new_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_booking_id;

    INSERT INTO booking_history (
        booking_id,
        old_status,
        new_status,
        changed_by,
        notes
    ) VALUES (
        p_booking_id,
        v_old_status,
        p_new_status,
        p_changed_by,
        p_notes
    );

    SELECT 'Booking status updated successfully' AS message;
END$$

-- Get Monthly Statistics Procedure
DROP PROCEDURE IF EXISTS `get_monthly_stats`$$
CREATE PROCEDURE `get_monthly_stats`(
    IN p_year INT,
    IN p_month INT
)
BEGIN
    SELECT
        DATE(booking_date) AS date,
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN status = 'completed' THEN package_price ELSE 0 END) AS revenue
    FROM
        bookings
    WHERE
        YEAR(booking_date) = p_year
        AND MONTH(booking_date) = p_month
    GROUP BY
        DATE(booking_date)
    ORDER BY
        date ASC;
END$$

-- Get Upcoming Bookings Procedure
DROP PROCEDURE IF EXISTS `get_upcoming_bookings`$$
CREATE PROCEDURE `get_upcoming_bookings`(
    IN p_days INT
)
BEGIN
    SELECT
        b.*,
        DATEDIFF(b.booking_date, CURDATE()) AS days_until
    FROM
        bookings b
    WHERE
        b.booking_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL p_days DAY)
        AND b.status IN ('pending', 'approved')
    ORDER BY
        b.booking_date ASC,
        b.booking_time ASC;
END$$

DELIMITER ;

-- =============================================
-- Default Data Insertion
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
('contact_map_embed', '', 'textarea'),
('doctor_years_experience', '15', 'text'),
('doctor_total_patients', '5000', 'text'),
('doctor_success_cases', '3500', 'text'),
('doctor_about_full', 'طبيب أسنان متخصص مع خبرة واسعة في جميع مجالات طب وجراحة الفم والأسنان', 'textarea')
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
('فحص شامل', 'فحص كامل للأسنان مع التنظيف', 500.00, '60 دقيقة', 'فحص شامل للأسنان\nتنظيف احترافي\nأشعة (إذا لزم الأمر)\nاستشارة', FALSE),
('تبييض الأسنان', 'تبييض احترافي للأسنان بأحدث التقنيات', 1500.00, '90 دقيقة', 'تبييض احترافي\nصور قبل وبعد\nمجموعة صيانة\nاستشارة متابعة', TRUE),
('تقويم الأسنان', 'استشارة تقويم الأسنان مع خطة العلاج', 3000.00, '45 دقيقة', 'استشارة كاملة\nخطة علاج مخصصة\nأشعة بانوراما\nمتابعة دورية', FALSE),
('زراعة الأسنان', 'استشارة زراعة الأسنان', 5000.00, '60 دقيقة', 'استشارة متخصصة\nفحص شامل\nأشعة ثلاثية الأبعاد\nخطة زراعة', FALSE),
('علاج الجذور', 'علاج عصب الأسنان', 800.00, '60 دقيقة', 'تخدير موضعي\nعلاج العصب\nحشو مؤقت\nمتابعة', FALSE),
('حشوات تجميلية', 'حشوات تجميلية بلون الأسنان', 600.00, '45 دقيقة', 'حشو تجميلي\nبلون الأسنان الطبيعي\nضمان لمدة سنة', FALSE)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Insert default menu items
INSERT INTO `menu_items` (`title`, `url`, `position`, `display_order`, `is_active`) VALUES
('الرئيسية', 'index.php', 'header', 1, 1),
('عن الدكتور', 'about.php', 'header', 2, 1),
('الخدمات', 'services.php', 'header', 3, 1),
('الباقات', 'packages.php', 'header', 4, 1),
('المقالات', 'blog.php', 'header', 5, 1),
('آراء العملاء', 'reviews.php', 'header', 6, 1),
('الرئيسية', 'index.php', 'footer', 1, 1),
('الخدمات', 'services.php', 'footer', 2, 1),
('الباقات', 'packages.php', 'footer', 3, 1),
('المقالات', 'blog.php', 'footer', 4, 1)
ON DUPLICATE KEY UPDATE `title` = `title`;

-- Insert sample doctors data
INSERT INTO `doctors` (`name`, `title`, `bio`, `specialization`, `years_experience`, `display_order`, `is_active`) VALUES
('د. أحمد محمد', 'استشاري طب وجراحة الفم والأسنان', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان التجميلي والعلاجي. متخصص في زراعة الأسنان وتجميل الابتسامة.', 'طب الأسنان التجميلي', 15, 1, 1),
('د. سارة علي', 'أخصائية تقويم الأسنان', 'خبيرة في تقويم الأسنان الشفاف والتقليدي. حاصلة على شهادات دولية في التقويم الحديث.', 'تقويم الأسنان', 10, 2, 1),
('د. محمد حسن', 'استشاري جراحة الفم والوجه والفكين', 'متخصص في جراحات الفم المعقدة وزراعة الأسنان المتقدمة. عضو الجمعية الأمريكية لجراحة الفم.', 'جراحة الفم والفكين', 12, 3, 1)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Insert sample bookings (for testing - delete in production)
INSERT INTO `bookings` (
    `client_name`,
    `client_email`,
    `client_phone`,
    `client_phone_carrier`,
    `client_whatsapp`,
    `client_whatsapp_carrier`,
    `booking_date`,
    `booking_time`,
    `booking_day`,
    `package_id`,
    `package_name`,
    `package_price`,
    `status`
) VALUES
('أحمد محمد', 'ahmed@example.com', '0101 234 5678', 'Vodafone', '0101 234 5678', 'Vodafone', '2025-12-25', '10:00 AM', 'الأربعاء', 1, 'فحص شامل', 500.00, 'pending'),
('فاطمة علي', 'fatima@example.com', '0122 345 6789', 'Orange', '0122 345 6789', 'Orange', '2025-12-26', '11:00 AM', 'الخميس', 2, 'تبييض الأسنان', 1500.00, 'approved'),
('محمود حسن', 'mahmoud@example.com', '0114 567 8901', 'Etisalat', '0114 567 8901', 'Etisalat', '2025-12-27', '02:00 PM', 'الجمعة', 3, 'تقويم الأسنان', 3000.00, 'pending'),
('سارة أحمد', 'sara@example.com', '0155 678 9012', 'WE', '0155 678 9012', 'WE', '2025-12-28', '03:00 PM', 'السبت', 1, 'فحص شامل', 500.00, 'completed')
ON DUPLICATE KEY UPDATE `client_name` = `client_name`;

-- Insert sample reviews
INSERT INTO `reviews` (`client_name`, `rating`, `review_text`, `is_approved`, `is_displayed`) VALUES
('محمد أحمد', 5, 'خدمة ممتازة والدكتور محترف جداً. أنصح بشدة!', 1, 1),
('سارة علي', 5, 'تجربة رائعة، العيادة نظيفة والطاقم لطيف ومحترم.', 1, 1),
('خالد حسن', 4, 'خدمة جيدة جداً وأسعار مناسبة.', 1, 1),
('فاطمة محمود', 5, 'أفضل عيادة أسنان! النتائج مذهلة.', 1, 1),
('أحمد سالم', 5, 'دكتور ماهر وخبير. راضي جداً عن العلاج.', 1, 1),
('نور الدين', 4, 'تجربة جيدة، الموعد كان منظم والانتظار قصير.', 1, 1)
ON DUPLICATE KEY UPDATE `client_name` = `client_name`;

-- =============================================
-- Email Templates
-- =============================================

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
'Email sent to client when booking is approved')
ON DUPLICATE KEY UPDATE `template_key` = `template_key`;

-- =============================================
-- Notes and Usage Instructions
-- =============================================

/*
✅ تم إنشاء قاعدة البيانات الكاملة بنجاح!

📋 الجداول المُنشأة:
━━━━━━━━━━━━━━━━━━━━
1. admin_users - مستخدمي لوحة التحكم
2. services - الخدمات
3. packages - الباقات
4. bookings - الحجوزات (مع كشف شركة الهاتف)
5. booking_history - سجل تغييرات الحجوزات
6. booking_stats - إحصائيات الحجوزات
7. blog_posts - المقالات
8. reviews - آراء العملاء
9. settings - إعدادات الموقع
10. sliders - السلايدر/البانرات
11. email_templates - قوالب البريد الإلكتروني
12. menu_items - عناصر القائمة
13. doctors - الأطباء

🔍 Views (الاستعلامات الجاهزة):
━━━━━━━━━━━━━━━━━━━━━━━━
1. active_bookings - الحجوزات النشطة
2. today_bookings - حجوزات اليوم
3. booking_summary - ملخص الحجوزات
4. popular_carriers - أكثر شركات الاتصالات استخداماً

⚙️ Stored Procedures:
━━━━━━━━━━━━━━━━━━
1. update_booking_status() - تحديث حالة الحجز
2. get_monthly_stats() - إحصائيات شهرية
3. get_upcoming_bookings() - الحجوزات القادمة

🔔 Triggers:
━━━━━━━━━━━
1. after_booking_status_update - حفظ تلقائي لسجل التغييرات

📱 شركات الاتصالات المدعومة:
━━━━━━━━━━━━━━━━━━━━━
- Vodafone: 010, 011
- Orange: 012
- Etisalat: 011
- WE: 015

🔐 بيانات الدخول الافتراضية:
━━━━━━━━━━━━━━━━━━━━━
اسم المستخدم: admin
كلمة المرور: admin123

⚠️ ملاحظات مهمة:
━━━━━━━━━━━━━━
1. قم بتغيير كلمة مرور الأدمن فوراً بعد التثبيت
2. احذف البيانات التجريبية من جدول bookings قبل الاستخدام الفعلي
3. تأكد من بيانات الاتصال في includes/config.php تطابق:
   - DB_HOST: localhost
   - DB_NAME: u186120816_kero_dentist
   - DB_USER: u186120816_kero_dentist
   - DB_PASS: 9Ea$eZnZ#

📊 استعلامات مفيدة:
━━━━━━━━━━━━━━━━
-- عرض الحجوزات النشطة
SELECT * FROM active_bookings;

-- عرض حجوزات اليوم
SELECT * FROM today_bookings;

-- إحصائيات شاملة
SELECT * FROM booking_summary;

-- أكثر الشركات استخداماً
SELECT * FROM popular_carriers;

-- تحديث حالة حجز
CALL update_booking_status(1, 'approved', 'admin', 'تم الموافقة');

-- إحصائيات ديسمبر 2025
CALL get_monthly_stats(2025, 12);

-- الحجوزات في الـ 7 أيام القادمة
CALL get_upcoming_bookings(7);

🚀 للبدء:
━━━━━━━━
1. استورد هذا الملف في phpMyAdmin أو MySQL
2. تأكد من بيانات الاتصال في includes/config.php
3. سجل الدخول إلى لوحة التحكم: admin/login.php
4. غير كلمة مرور الأدمن من الإعدادات

✨ تم إنشاء هذا الملف بواسطة Claude AI
📅 التاريخ: ديسمبر 2025
🔖 النسخة: 2.0 (Unified Database)
*/

-- =============================================
-- End of Complete Database Schema
-- =============================================
