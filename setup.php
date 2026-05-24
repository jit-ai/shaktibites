<?php
/**
 * Setup script for Shakti Bites
 * Run this in your browser to initialize the database
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shakti Bites - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Database Setup</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Show form if not submitted
                    if (!isset($_POST['setup_db'])) {
                    ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" name="db_host" value="localhost" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="db_name" value="shakti_bites" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Username</label>
                            <input type="text" class="form-control" name="db_user" value="root" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="db_pass" value="">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="setup_db" class="btn btn-primary">Setup Database</button>
                        </div>
                    </form>
                    <?php
                    } else {
                        // Process database setup
                        $host = $_POST['db_host'] ?? 'localhost';
                        $db   = $_POST['db_name'] ?? 'shakti_bites';
                        $user = $_POST['db_user'] ?? 'root';
                        $pass = $_POST['db_pass'] ?? '';
                        $charset = 'utf8mb4';
                        
                        echo "<div class='alert alert-info'>Connecting to database...</div>";
                        flush();
                        
                        try {
                            // First connect without database to create it
                            $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                            ]);
                            $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
                            echo "<div class='alert alert-success'>Database '$db' created or already exists.</div>";
                            flush();
                            
                            // Now connect to the database
                            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                            $options = [
                                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES   => false,
                            ];
                            
                            $pdo = new PDO($dsn, $user, $pass, $options);
                            echo "<div class='alert alert-success'>Connected to database.</div>";
                            flush();
                            
                            // Begin transaction
                            $pdo->beginTransaction();
                            
                            // Drop tables if they exist (for clean migration)
                            $tables = ['order_items', 'orders', 'cart_items', 'products', 'categories', 'users'];
                            foreach ($tables as $table) {
                                $pdo->exec("DROP TABLE IF EXISTS $table");
                            }
                            echo "<div class='alert alert-info'>Cleaned up existing tables.</div>";
                            flush();
                            
                            // Create users table
                            $usersSql = "
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
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($usersSql);
                            echo "<div class='alert alert-info'>Users table created.</div>";
                            flush();
                            
                            // Create categories table
                            $categoriesSql = "
                                CREATE TABLE categories (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(100) NOT NULL,
                                    description TEXT,
                                    image VARCHAR(255),
                                    is_active TINYINT(1) DEFAULT 1,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($categoriesSql);
                            echo "<div class='alert alert-info'>Categories table created.</div>";
                            flush();
                            
                            // Create products table
                            $productsSql = "
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
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($productsSql);
                            echo "<div class='alert alert-info'>Products table created.</div>";
                            flush();
                            
                            // Create cart_items table
                            $cartItemsSql = "
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
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($cartItemsSql);
                            echo "<div class='alert alert-info'>Cart items table created.</div>";
                            flush();
                            
                            // Create orders table
                            $ordersSql = "
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
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($ordersSql);
                            echo "<div class='alert alert-info'>Orders table created.</div>";
                            flush();
                            
                            // Create order_items table
                            $orderItemsSql = "
                                CREATE TABLE order_items (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    order_id INT,
                                    product_id INT,
                                    quantity INT NOT NULL,
                                    price DECIMAL(10,2) NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                            ";
                            $pdo->exec($orderItemsSql);
                            echo "<div class='alert alert-info'>Order items table created.</div>";
                            flush();
                            
                            // Insert default admin user
                            $adminSql = "
                                INSERT INTO users (name, email, password, phone, address, is_admin, is_active) 
                                VALUES ('Admin User', 'admin@shaktibites.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'Admin Address', 1, 1)
                            ";
                            $pdo->exec($adminSql);
                            echo "<div class='alert alert-info'>Default admin user created.</div>";
                            flush();
                            
                            // Insert sample categories
                            $categoriesSql = "
                                INSERT INTO categories (name, description, image, is_active) VALUES 
                                ('Energy Bites', 'High protein energy bites for instant energy', 'energy.jpg', 1),
                                ('Protein Snacks', 'Healthy protein-rich snacks', 'protein.jpg', 1),
                                ('Combo Packs', 'Value combo packs with multiple flavors', 'combo.jpg', 1)
                            ";
                            $pdo->exec($categoriesSql);
                            echo "<div class='alert alert-info'>Sample categories inserted.</div>";
                            flush();
                            
                            // Insert sample products
                            $productsSql = "
                                INSERT INTO products (name, description, price, image, label, label_class, category_id, is_active, stock, sku) VALUES 
                                ('Peanut Jaggery Power Bites', 'Experience the perfect blend of roasted peanuts and jaggery in every bite. Our Peanut Jaggery Power Bites are crafted to give you instant energy without any sugar crash. Made with premium peanuts, organic jaggery, and dates, these laddoos pack a powerful punch of protein and natural goodness.', 249, 'product1.PNG', 'Everyday Energy', 'label-everyday', 1, 1, 100, 'SB-PROD-001'),
                                ('Almond Cacao Power Bites', 'Indulge in the rich, chocolatey goodness of our Almond Cacao Power Bites. Made with premium almonds, raw cacao powder, and dates, these laddoos satisfy your chocolate cravings while providing the energy you need. The perfect guilt-free treat for chocolate lovers.', 279, 'product2.PNG', '⭐ Best Seller ⭐', 'label-bestseller', 1, 1, 100, 'SB-PROD-002'),
                                ('Dry Fruit Cardamom Bites', 'Savor the royal taste of our Dry Fruit Cardamom Bites. Packed with premium cashews, almonds, and aromatic cardamom, these laddoos offer a luxurious snacking experience. The perfect blend of tradition and nutrition in every bite.', 299, 'product3.PNG', 'Premium Pick', 'label-premium', 1, 1, 100, 'SB-PROD-003')
                            ";
                            $pdo->exec($productsSql);
                            echo "<div class='alert alert-info'>Sample products inserted.</div>";
                            flush();
                            
                            // Commit transaction
                            $pdo->commit();
                            echo "<div class='alert alert-success'>Database migration completed successfully!</div>";
                            echo "<div class='mt-3'><a href='../index.php' class='btn btn-primary'>Go to Homepage</a></div>";
                            
                        } catch (Exception $e) {
                            // Rollback on error
                            if (isset($pdo)) {
                                $pdo->rollBack();
                            }
                            echo "<div class='alert alert-danger'>Migration failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>