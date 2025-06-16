-- ===========================================
-- ✅ جدول المشاريع
-- ===========================================
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `price` VARCHAR(255) NOT NULL,
    `beds` INT NOT NULL DEFAULT 0,
    `baths` INT NOT NULL DEFAULT 0,
    `size` VARCHAR(255) NOT NULL DEFAULT '0',
    `area` VARCHAR(255) DEFAULT NULL,
    `video_url` VARCHAR(255) DEFAULT NULL,
    `subtitle` VARCHAR(255) DEFAULT NULL,
    `description` TEXT,
    `details` TEXT,
    `extra_title` VARCHAR(255) DEFAULT NULL,
    `extra_text` TEXT,
    `extra_image` VARCHAR(255) DEFAULT NULL,
    `main_media` VARCHAR(255) DEFAULT NULL,
    `last_title` VARCHAR(255) DEFAULT NULL,
    `last_text` TEXT DEFAULT NULL,
    `last_image` VARCHAR(255) DEFAULT NULL,
    `views` INT NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول التفاصيل الفرعية للمشروع
-- ===========================================
CREATE TABLE IF NOT EXISTS `project_table` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `col1` VARCHAR(255) NOT NULL,
    `col2` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الصور المتعددة للمشروع
-- ===========================================
CREATE TABLE IF NOT EXISTS `project_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول البلوكات الفرعية للمشروع
-- ===========================================
CREATE TABLE IF NOT EXISTS `project_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `block_title` VARCHAR(255) NOT NULL,
    `block_text` TEXT NOT NULL,
    `block_image` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول سجل المشاهدات للمشروع
-- ===========================================
CREATE TABLE IF NOT EXISTS `project_views` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `visitor_ip` VARCHAR(255) NOT NULL,
    `visit_date` DATETIME NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول طرق الدفع
-- ===========================================
CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `method_name` VARCHAR(255) NOT NULL,
    `account_number` VARCHAR(255) DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `title` VARCHAR(255) DEFAULT NULL,
    `text` TEXT DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الحجوزات
-- ===========================================
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `payment_method_id` INT DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL DEFAULT 0,
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الزوار
-- ===========================================
CREATE TABLE IF NOT EXISTS `visitors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `payment_method_id` INT DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `visit_date` DATE DEFAULT NULL,
    `visit_time` TIME DEFAULT NULL,
    `amount` DECIMAL(10, 2) DEFAULT NULL,
    `payment_receipt` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول طلبات المشاريع
-- ===========================================
CREATE TABLE IF NOT EXISTS `project_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `project_title` VARCHAR(255) NOT NULL,
    `project_location` VARCHAR(255) NOT NULL,
    `project_image` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول السلايدر
-- ===========================================
CREATE TABLE IF NOT EXISTS `sliders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول سلايدر من نحن
-- ===========================================
CREATE TABLE IF NOT EXISTS `about_slider` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول كروت من نحن
-- ===========================================
CREATE TABLE IF NOT EXISTS `about_cards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `link` VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول أبرز ما يميزنا
-- ===========================================
CREATE TABLE IF NOT EXISTS `highlights` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الفيديوهات
-- ===========================================
CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `url` VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الإعلانات
-- ===========================================
CREATE TABLE IF NOT EXISTS `ads` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول أيقونات الإعلانات
-- ===========================================
CREATE TABLE IF NOT EXISTS `ad_icons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ad_id` INT NOT NULL,
    `icon` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الأسئلة الشائعة
-- ===========================================
CREATE TABLE IF NOT EXISTS `questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `image` VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ===========================================
-- ✅ جدول الخدمات
-- ===========================================
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `icon` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;