CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    location TEXT NOT NULL,
    title TEXT NOT NULL,
    price VARCHAR(100) NOT NULL,
    beds INT NOT NULL DEFAULT 0,
    baths INT NOT NULL DEFAULT 0,
    size VARCHAR(100) NOT NULL DEFAULT '0',
    area TEXT,
    video_url TEXT,
    subtitle TEXT,
    description TEXT,
    details TEXT,
    extra_title VARCHAR(255),
    extra_text TEXT,
    extra_image TEXT,
    main_media TEXT,
    last_title VARCHAR(255),
    last_text TEXT,
    last_image TEXT,
    views INT NOT NULL DEFAULT 0,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    col1 LONGTEXT NOT NULL,
    col2 LONGTEXT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image LONGTEXT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS project_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    block_title LONGTEXT NOT NULL,
    block_text LONGTEXT NOT NULL,
    block_image LONGTEXT,
    ad_id INT,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS project_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    visitor_ip VARCHAR(100) NOT NULL,
    visit_date DATETIME NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(255) NOT NULL,
    account_number VARCHAR(255),
    details TEXT,
    title VARCHAR(255),
    text TEXT,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    payment_method_id INT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    payment_method_id INT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    visit_date DATE,
    visit_time TIME,
    amount DECIMAL(10, 2),
    payment_receipt TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE SET NULL,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS project_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    project_title VARCHAR(255) NOT NULL,
    project_location VARCHAR(255) NOT NULL,
    project_image TEXT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS about_slider (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS about_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    link TEXT,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS highlights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url TEXT NOT NULL,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS ad_icons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    icon TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    text TEXT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    image TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    ad_id INT,
    FOREIGN KEY (ad_id) REFERENCES ads (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS booking_info_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    text TEXT NOT NULL,
    image TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS info_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    phone VARCHAR(20),
    username VARCHAR(255),
    amount DECIMAL(10, 2),
    payment_method VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    record_id INT NOT NULL,
    username VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO
    users (
        username,
        email,
        password,
        role
    )
VALUES (
        'admin',
        'admin@example.com',
        '$2y$10$qwAnquVu0UV0NRKhv7Qm/eaIvTTpHddN0AgZZSl63EvgyA6PN6RiK',
        'admin'
    );

DROP TABLE IF EXISTS plan_and_room;

CREATE TABLE plan_and_room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT,
    title VARCHAR(255),
    description TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE plan_and_room_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    image TEXT,
    title VARCHAR(255),
    description TEXT,
    action VARCHAR(50),
    user VARCHAR(50),
    date DATETIME
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE property_highlights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT,
    title VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS about_team_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS about_director_card (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    text TEXT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS about_initiatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image TEXT NOT NULL,
    title VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    link TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE new_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('announcement', 'service') NOT NULL,
    image TEXT,
    title VARCHAR(255),
    description TEXT,
    link TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE site_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(100),
    visit_time DATETIME DEFAULT CURRENT_TIMESTAMP
);