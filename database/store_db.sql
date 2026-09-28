CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Donut',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data Awal (Dummy Menu)
INSERT INTO products (name, category, price, stock, image) VALUES
('Glazed Classic Donut', 'Donut', 12000.00, 25, 'default.png'),
('Matcha Artisan Latte', 'Minuman', 28000.00, 15, 'default.png'),
('Choco Rainbow Sprinkles', 'Donut', 15000.00, 20, 'default.png');