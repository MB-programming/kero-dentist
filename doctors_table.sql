-- إضافة جدول الدكاترة
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

-- إضافة بيانات تجريبية
INSERT INTO `doctors` (`name`, `title`, `bio`, `specialization`, `years_experience`, `display_order`, `is_active`) VALUES
('د. أحمد محمد', 'استشاري طب وجراحة الفم والأسنان', 'خبرة تمتد لأكثر من 15 عاماً في مجال طب الأسنان التجميلي والعلاجي. متخصص في زراعة الأسنان وتجميل الابتسامة.', 'طب الأسنان التجميلي', 15, 1, 1),
('د. سارة علي', 'أخصائية تقويم الأسنان', 'خبيرة في تقويم الأسنان الشفاف والتقليدي. حاصلة على شهادات دولية في التقويم الحديث.', 'تقويم الأسنان', 10, 2, 1),
('د. محمد حسن', 'استشاري جراحة الفم والوجه والفكين', 'متخصص في جراحات الفم المعقدة وزراعة الأسنان المتقدمة. عضو الجمعية الأمريكية لجراحة الفم.', 'جراحة الفم والفكين', 12, 3, 1);
