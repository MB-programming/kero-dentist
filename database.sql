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
    `client_address` TEXT,
    `client_phone` VARCHAR(20) NOT NULL,
    `client_whatsapp` VARCHAR(20) NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_day` VARCHAR(20) NOT NULL,
    `package_id` INT,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    `whatsapp_sent` BOOLEAN DEFAULT FALSE,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE SET NULL
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
('hero_subtitle', 'Professional dental care for you and your family', 'text')
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

-- =============================================
-- End of SQL File
-- تم بنجاح! يمكنك الآن استخدام الموقع
-- =============================================
