-- Shakti Bites Database Schema
-- This file contains all SQL statements needed to set up the database
-- Run this in MySQL: source /path/to/shaktibites.sql

-- Drop existing tables (if they exist) for clean installation
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    label VARCHAR(100),
    label_class VARCHAR(50),
    category_id INT,
    is_active TINYINT(1) DEFAULT 1,
    stock INT DEFAULT 0,
    sku VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create cart_items table
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cod',
    payment_status VARCHAR(20) DEFAULT 'pending',
    order_status VARCHAR(20) DEFAULT 'pending',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
-- Password: 'password' (hashed with bcrypt)
INSERT INTO users (name, email, password, phone, address, is_admin) 
VALUES ('Admin User', 'admin@shaktibites.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'Admin Address', 1);

-- Insert sample categories
INSERT INTO categories (name, description, image, is_active) VALUES 
('Energy Bites', 'High protein energy bites for instant energy', 'energy.jpg', 1),
('Protein Snacks', 'Healthy protein-rich snacks', 'protein.jpg', 1),
('Combo Packs', 'Value combo packs with multiple flavors', 'combo.jpg', 1);

-- Insert sample products
INSERT INTO products (name, description, price, image, label, label_class, category_id, is_active, stock, sku) VALUES 
('Peanut Jaggery Power Bites', 'Experience the perfect blend of roasted peanuts and jaggery in every bite. Our Peanut Jaggery Power Bites are crafted to give you instant energy without any sugar crash. Made with premium peanuts, organic jaggery, and dates, these laddoos pack a powerful punch of protein and natural goodness.', 249, 'product1.PNG', 'Everyday Energy', 'label-everyday', 1, 1, 100, 'SB-PROD-001'),
('Almond Cacao Power Bites', 'Indulge in the rich, chocolatey goodness of our Almond Cacao Power Bites. Made with premium almonds, raw cacao powder, and dates, these laddoos satisfy your chocolate cravings while providing the energy you need. The perfect guilt-free treat for chocolate lovers.', 279, 'product2.PNG', '⭐ Best Seller ⭐', 'label-bestseller', 1, 1, 100, 'SB-PROD-002'),
('Dry Fruit Cardamom Bites', 'Savor the royal taste of our Dry Fruit Cardamom Bites. Packed with premium cashews, almonds, and aromatic cardamom, these laddoos offer a luxurious snacking experience. The perfect blend of tradition and nutrition in every bite.', 299, 'product3.PNG', 'Premium Pick', 'label-premium', 1, 1, 100, 'SB-PROD-003');