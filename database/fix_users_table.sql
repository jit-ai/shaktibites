-- To fix the database schema issue, please run this SQL command to add the is_active column to the users table:
ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER is_admin;

-- After running this, the users table structure will be:
-- id INT AUTO_INCREMENT PRIMARY KEY,
-- name VARCHAR(100) NOT NULL,
-- email VARCHAR(100) NOT NULL UNIQUE,
-- password VARCHAR(255) NOT NULL,
-- phone VARCHAR(15),
-- address TEXT,
-- is_admin TINYINT(1) DEFAULT 0,
-- is_active TINYINT(1) DEFAULT 1,
-- created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-- updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP