<?php
/**
 * Migration script to fix missing columns in existing database
 */
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost';
$db   = 'shakti_bites';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<h2>Database Migration</h2>";
    
    // Check if is_active column exists in users table
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_active'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1");
        echo "<p style='color:green'>Added is_active column to users table.</p>";
    } else {
        echo "<p>is_active column already exists in users table.</p>";
    }
    
    // Check if updated_at column exists in users table
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'updated_at'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "<p style='color:green'>Added updated_at column to users table.</p>";
    } else {
        echo "<p>updated_at column already exists in users table.</p>";
    }
    
    echo "<p style='color:green;font-weight:bold'>Migration completed successfully!</p>";
    echo "<a href='../admin/users.php'>Go to Users Page</a>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Migration failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}