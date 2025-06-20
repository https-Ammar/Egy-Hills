-- جدول الإعلانات
CREATE TABLE IF NOT EXISTS `ads` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول المشاريع
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
    `views` INT NOT NULL DEFAULT 0,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول بيانات إضافية للمشاريع
CREATE TABLE IF NOT EXISTS `project_table` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `col1` VARCHAR(255) NOT NULL,
    `col2` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول صور المشاريع
CREATE TABLE IF NOT EXISTS `project_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول كتل المشروع
CREATE TABLE IF NOT EXISTS `project_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `block_title` VARCHAR(255) NOT NULL,
    `block_text` TEXT NOT NULL,
    `block_image` VARCHAR(255) DEFAULT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول تسجيل زيارات المشاريع
CREATE TABLE IF NOT EXISTS `project_views` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `visitor_ip` VARCHAR(255) NOT NULL,
    `visit_date` DATETIME NOT NULL,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول طرق الدفع
CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `method_name` VARCHAR(255) NOT NULL,
    `account_number` VARCHAR(255) DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `title` VARCHAR(255) DEFAULT NULL,
    `text` TEXT DEFAULT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول الحجوزات
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

-- جدول الزوار
CREATE TABLE IF NOT EXISTS `visitors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT DEFAULT NULL,
    `payment_method_id` INT DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `visit_date` DATE DEFAULT NULL,
    `visit_time` TIME DEFAULT NULL,
    `amount` DECIMAL(10, 2) DEFAULT NULL,
    `payment_receipt` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول طلبات المشاريع
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

-- جدول السلايدر (العرض المتحرك)
CREATE TABLE IF NOT EXISTS `sliders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول السلايدر الخاص بالصفحة التعريفية
CREATE TABLE IF NOT EXISTS `about_slider` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول بطاقات الصفحة التعريفية
CREATE TABLE IF NOT EXISTS `about_cards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `link` VARCHAR(255),
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول المميزات البارزة
CREATE TABLE IF NOT EXISTS `highlights` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول الفيديوهات
CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `url` VARCHAR(255) NOT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول أيقونات الإعلانات
CREATE TABLE IF NOT EXISTS `ad_icons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ad_id` INT NOT NULL,
    `icon` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول الأسئلة الشائعة
CREATE TABLE IF NOT EXISTS `questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `image` VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول الخدمات
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `icon` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `ad_id` INT DEFAULT NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول كتل معلومات الحجز
CREATE TABLE IF NOT EXISTS `booking_info_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول كتل المعلومات العامة
CREATE TABLE IF NOT EXISTS `info_blocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول سجل العمليات (Logs)
CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `action` VARCHAR(50) NOT NULL,
    `table_name` VARCHAR(100) NOT NULL,
    `record_id` INT NOT NULL,
    `username` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

DROP TABLE IF EXISTS plan_and_room;

CREATE TABLE plan_and_room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    title VARCHAR(255),
    description TEXT
);

CREATE TABLE plan_and_room_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    image VARCHAR(255),
    title VARCHAR(255),
    description TEXT,
    action VARCHAR(50),
    user VARCHAR(50),
    date DATETIME
);

CREATE TABLE property_highlights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    title VARCHAR(255)
);

-- about us page .php

-- كروت فريق التطوير
CREATE TABLE IF NOT EXISTS `about_team_cards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- كارت رئيس التصوير
CREATE TABLE IF NOT EXISTS `about_director_card` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- كروت المبادرات
CREATE TABLE IF NOT EXISTS `about_initiatives` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;