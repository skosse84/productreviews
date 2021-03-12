CREATE DATABASE IF NOT EXISTS agregator COLLATE utf8_general_ci;

use agregator;

CREATE TABLE IF NOT EXISTS product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL UNIQUE,
    price INT NOT NULL,
    img_name VARCHAR(255) NOT NULL,
    description TEXT,
    author VARCHAR(255) NOT NULL,
    rev_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)  ENGINE=INNODB DEFAULT COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    author VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating>0 and rating<11),
    comment_text VARCHAR(511) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)  ENGINE=INNODB DEFAULT COLLATE utf8mb4_unicode_ci;