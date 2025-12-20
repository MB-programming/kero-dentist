-- ===================================================================
-- Kero Dentist - Booking Database SQL
-- قاعدة بيانات الحجوزات للعيادة
-- Created: 2025
-- ===================================================================

-- استخدام قاعدة البيانات
-- ملاحظة: قاعدة البيانات موجودة بالفعل على السيرفر
USE `u186120816_kero_dentist`;

-- ===================================================================
-- جدول الحجوزات (Bookings Table)
-- ===================================================================

DROP TABLE IF EXISTS `bookings`;

CREATE TABLE `bookings` (
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
  `notes` TEXT DEFAULT NULL COMMENT 'ملاحظات إضافية',
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

-- ===================================================================
-- جدول الباقات (Packages Table)
-- نسخة من الباقات في قاعدة البيانات الرئيسية للمرجعية
-- ===================================================================

DROP TABLE IF EXISTS `packages`;

CREATE TABLE `packages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'اسم الباقة',
  `description` TEXT DEFAULT NULL COMMENT 'وصف الباقة',
  `price` DECIMAL(10,2) NOT NULL COMMENT 'السعر',
  `duration` VARCHAR(100) DEFAULT NULL COMMENT 'المدة',
  `features` TEXT DEFAULT NULL COMMENT 'المميزات (JSON)',
  `icon` VARCHAR(100) DEFAULT NULL COMMENT 'أيقونة الباقة',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'نشط؟',
  `display_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'ترتيب العرض',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='باقات الخدمات';

-- ===================================================================
-- جدول سجل الحجوزات (Booking History Log)
-- لحفظ جميع التغييرات على الحجوزات
-- ===================================================================

DROP TABLE IF EXISTS `booking_history`;

CREATE TABLE `booking_history` (
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

-- ===================================================================
-- جدول إحصائيات الحجوزات (Booking Statistics)
-- للتقارير والإحصائيات
-- ===================================================================

DROP TABLE IF EXISTS `booking_stats`;

CREATE TABLE `booking_stats` (
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

-- ===================================================================
-- Triggers للحفاظ على سجل التغييرات
-- ===================================================================

DELIMITER $$

-- Trigger عند تغيير حالة الحجز
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

-- ===================================================================
-- Views مفيدة
-- ===================================================================

-- View للحجوزات النشطة (المعلقة والمؤكدة)
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

-- View للحجوزات اليوم
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

-- View لإحصائيات شاملة
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

-- View لأكثر الشركات استخداماً
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

-- ===================================================================
-- بيانات تجريبية (Optional - يمكن حذفها في الإنتاج)
-- ===================================================================

-- إضافة باقات تجريبية
INSERT INTO `packages` (`name`, `description`, `price`, `duration`, `icon`, `is_active`, `display_order`) VALUES
('فحص شامل', 'فحص كامل للأسنان مع التنظيف', 500.00, '60 دقيقة', 'fa-tooth', 1, 1),
('تبييض الأسنان', 'تبييض احترافي للأسنان بأحدث التقنيات', 1500.00, '90 دقيقة', 'fa-smile', 1, 2),
('تقويم الأسنان', 'استشارة تقويم الأسنان مع خطة العلاج', 3000.00, '45 دقيقة', 'fa-teeth', 1, 3),
('زراعة الأسنان', 'استشارة زراعة الأسنان', 5000.00, '60 دقيقة', 'fa-tooth', 1, 4),
('علاج الجذور', 'علاج عصب الأسنان', 800.00, '60 دقيقة', 'fa-tooth', 1, 5),
('حشوات تجميلية', 'حشوات تجميلية بلون الأسنان', 600.00, '45 دقيقة', 'fa-tooth', 1, 6);

-- إضافة حجوزات تجريبية
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
('سارة أحمد', 'sara@example.com', '0155 678 9012', 'WE', '0155 678 9012', 'WE', '2025-12-28', '03:00 PM', 'السبت', 1, 'فحص شامل', 500.00, 'completed');

-- ===================================================================
-- Stored Procedures مفيدة
-- ===================================================================

DELIMITER $$

-- Procedure لتحديث حالة الحجز
DROP PROCEDURE IF EXISTS `update_booking_status`$$

CREATE PROCEDURE `update_booking_status`(
    IN p_booking_id INT,
    IN p_new_status VARCHAR(50),
    IN p_changed_by VARCHAR(100),
    IN p_notes TEXT
)
BEGIN
    DECLARE v_old_status VARCHAR(50);

    -- الحصول على الحالة القديمة
    SELECT status INTO v_old_status
    FROM bookings
    WHERE id = p_booking_id;

    -- تحديث الحالة
    UPDATE bookings
    SET status = p_new_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_booking_id;

    -- إضافة سجل في الـ history
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

-- Procedure للحصول على إحصائيات شهرية
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

-- Procedure للحصول على الحجوزات القادمة
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

-- ===================================================================
-- منح الصلاحيات (تأكد من تغيير اسم المستخدم في الإنتاج)
-- ===================================================================

-- GRANT ALL PRIVILEGES ON u186120816_kero_dentist.* TO 'u186120816_kero_dentist'@'localhost';
-- FLUSH PRIVILEGES;

-- ===================================================================
-- ملاحظات مهمة
-- ===================================================================

/*
1. تم إنشاء قاعدة بيانات منفصلة للحجوزات لتحسين الأمان والأداء
2. جميع الجداول تستخدم InnoDB engine لدعم Transactions
3. تم إضافة Indexes على الحقول المهمة لتحسين الأداء
4. تم إضافة Triggers لحفظ سجل التغييرات تلقائياً
5. تم إضافة Views مفيدة للاستعلامات الشائعة
6. تم إضافة Stored Procedures للعمليات المتكررة

للاستخدام:
- قم بتشغيل هذا الملف في MySQL/phpMyAdmin
- تأكد من تحديث بيانات الاتصال في ملف booking-db-connection.php
- احذف البيانات التجريبية قبل النشر في الإنتاج

للنسخ الاحتياطي:
mysqldump -u u186120816_kero_dentist -p u186120816_kero_dentist > backup.sql

للاستعادة:
mysql -u u186120816_kero_dentist -p u186120816_kero_dentist < backup.sql
*/

-- ===================================================================
-- انتهى
-- ===================================================================
